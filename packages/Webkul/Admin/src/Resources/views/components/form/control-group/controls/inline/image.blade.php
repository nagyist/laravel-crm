<v-inline-image-edit {{ $attributes }}>
    <div class="group w-full max-w-full hover:rounded-sm">
        <div class="rounded-xs flex h-[34px] items-center pl-2.5 text-left">
            <div class="shimmer h-5 w-48 rounded border border-transparent"></div>
        </div>
    </div>
</v-inline-image-edit>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inline-image-edit-template"
    >
        <div class="group w-full max-w-full hover:rounded-sm">
            <!-- Non-editing view -->
            <div
                v-if="! isEditing"
                class="rounded-xs flex h-[38px] items-center"
                :class="allowEdit ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : ''"
                :style="textPositionStyle"
            >
                <x-admin::form.control-group.control
                    type="hidden"
                    ::id="name"
                    ::name="name"
                    v-model="inputValue"
                />

                <span class="pl-[2px] text-sm font-normal">
                    <a 
                        :href="inputValue" 
                        target="_blank"
                    >
                        <img
                            :src="inputValue"
                            class="h-10 w-10"
                            :alt="name"
                        />
                    </a>
                </span>

                <template v-if="allowEdit">
                    <i
                        @click="toggle"
                        class="icon-edit cursor-pointer rounded text-2xl opacity-0 hover:bg-gray-200 group-hover:opacity-100 dark:hover:bg-gray-950"
                    ></i>
                </template>
            </div>
        
            <!-- Editing view -->
            <div
                class="relative flex w-full flex-col"
                v-else
            >
                <div class="relative flex w-full flex-col">
                    <input
                        type="file"
                        :name="name"
                        :id="name"
                        :class="[errors.length ? 'border !border-red-600 hover:border-red-600' : '']"
                        class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-gray-800 dark:file:dark:text-white dark:hover:border-gray-400 dark:focus:border-gray-400"
                        @change="handleChange"
                        ref="input"
                    />
                        
                    <!-- Action Buttons -->
                    <div class="absolute top-1/2 flex -translate-y-1/2 transform gap-0.5 ltr:right-2 rtl:left-2">
                        <button
                            type="button"
                            class="flex items-center justify-center bg-green-100 p-1 hover:bg-green-200 ltr:rounded-l-md rtl:rounded-r-md"
                            @click="save"
                        >
                            <i class="icon-tick text-md cursor-pointer font-bold text-green-600 dark:!text-green-600" />
                        </button>
                    
                        <button
                            type="button"
                            class="flex items-center justify-center bg-red-100 p-1 hover:bg-red-200 ltr:rounded-r-md rtl:rounded-l-md"
                            @click="cancel"
                        >
                            <i class="icon-cross-large text-md cursor-pointer font-bold text-red-600 dark:!text-red-600" />
                        </button>
                    </div>
                </div>

                <x-admin::form.control-group.error ::name="name"/>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-inline-image-edit', {
            template: '#v-inline-image-edit-template',

            emits: ['on-change', 'on-cancelled'],

            props: {
                name: {
                    type: String,
                    required: true,
                },

                value: {
                    required: true,
                },

                rules: {
                    type: String,
                    default: '',
                },

                label: {
                    type: String,
                    default: '',
                },

                placeholder: {
                    type: String,
                    default: '',
                },

                position: {
                    type: String,
                    default: 'right',
                },

                allowEdit: {
                    type: Boolean,
                    default: true,
                },

                errors: {
                    type: Object,
                    default: {},
                },

                url: {
                    type: String,
                    default: '',
                },
            },

            data() {
                return {
                    inputValue: this.value,

                    isEditing: false,

                    file: null,

                    isRTL: document.documentElement.dir === 'rtl',
                };
            },

            watch: {
                /**
                 * Watch the value prop.
                 * 
                 * @param {String} newValue 
                 */
                value(newValue) {
                    this.inputValue = newValue;
                },
            },

            computed: {
                /**
                 * Get the input position style.
                 * 
                 * @return {String}
                 */
                 inputPositionStyle() {
                    return this.position === 'left' 
                        ? this.isRTL 
                            ? 'text-align: right; padding-right: 9px;' 
                            : 'text-align: left; padding-left: 9px;'
                        : this.isRTL 
                            ? 'text-align: left; padding-left: 9px;' 
                            : 'text-align: right; padding-right: 9px;';
                },

                /**
                 * Get the text position style.
                 * 
                 * @return {String}
                 */
                textPositionStyle() {
                    return this.position === 'left'  ? this.isRTL 
                            ? 'justify-content: end;' 
                            : 'justify-content: space-between;' 
                        : this.isRTL 
                            ? 'justify-content: space-between;' 
                            : 'justify-content: end;';
                },
            },

            methods: {
                /**
                 * Toggle the input.
                 * 
                 * @return {void}
                 */
                toggle() {
                    this.isEditing = true;
                },

                /**
                 * Save the input value.
                 * 
                 * @return {void}
                 */
                save() {
                    if (this.errors[this.name]) {
                        return;
                    }

                    this.isEditing = false;

                    let formData = new FormData();

                    formData.append(this.name, this.file);

                    formData.append('_method', 'PUT');

                    if (this.url) {
                        this.$axios.post(this.url, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then((response) => {
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch((error) => {
                            console.error(error);
                            this.inputValue = this.value;
                        });
                    }

                    this.$emit('on-change', {
                        name: this.name,
                        value: this.inputValue,
                    });
                },

                /**
                 * Cancel the input value.
                 * 
                 * @return {void}
                 */
                cancel() {
                    this.inputValue = this.value;

                    this.isEditing = false;

                    this.$emit('on-cancelled', {
                        name: this.name,
                        value: this.inputValue,
                    });
                },

                handleChange(event) {
                    this.file = event.target.files[0];

                    this.inputValue = URL.createObjectURL(this.file);
                },
            },
        });
    </script>
@endPushOnce