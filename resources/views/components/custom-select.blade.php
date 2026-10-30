@props(['name', 'options' => [], 'selected' => '', 'placeholder' => 'Pilih opsi...', 'onchange' => null, 'width' => 'w-full'])

<div x-data="{
        open: false,
        value: '{{ $selected }}',
        options: {{ json_encode($options) }},
        onChangeAction: '{{ $onchange }}',
        get selectedLabel() {
            const option = this.options.find(opt => opt.value == this.value);
            return option ? option.label : '{{ $placeholder }}';
        },
        select(val) {
            this.value = val;
            this.open = false;
            
            // Paksa update nilai DOM secara instan sebelum fungsi submit dijalankan
            // Mengatasi delay reaktivitas Alpine.js (bug harus klik 2x)
            this.$refs.hiddenInput.value = val;
            
            if(this.onChangeAction) {
                // Gunakan nextTick untuk amannya jika form parent butuh waktu update
                this.$nextTick(() => {
                    eval(this.onChangeAction);
                });
            }
        }
    }" 
    class="relative {{ $width }}"
    @keydown.escape.prevent="open = false"
    @click.outside="open = false">
    
    <input type="hidden" x-ref="hiddenInput" name="{{ $name }}" :value="value">

    <button type="button" 
            @click="open = !open" 
            :aria-expanded="open"
            class="relative w-full rounded-xl border-0 py-2.5 pl-4 pr-10 text-left text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm font-medium bg-slate-50 hover:bg-white transition-all duration-200 cursor-pointer">
        <span class="block truncate" x-text="selectedLabel"></span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="h-5 w-5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-indigo-500' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <ul x-show="open" 
        x-transition:enter="transition ease-out duration-150" 
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]" 
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
        x-transition:leave="transition ease-in duration-100" 
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]" 
        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-xl bg-white/90 backdrop-blur-xl py-1 text-base shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" 
        style="display: none;"
        role="listbox">
        
        <template x-for="option in options" :key="option.value">
            <li @click="select(option.value)" 
                class="relative cursor-pointer select-none py-2.5 pl-4 pr-9 transition-colors"
                :class="value == option.value ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-indigo-600'"
                role="option">
                <span class="block truncate" x-text="option.label"></span>
                
                <!-- Checkmark for selected item -->
                <span x-show="value == option.value" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                </span>
            </li>
        </template>
    </ul>
</div>
