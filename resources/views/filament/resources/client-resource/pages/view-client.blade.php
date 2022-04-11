<x-filament::page>
    <x-tables::container>
        <div class="overflow-y-auto relative rounded-t-xl">
            <x-tables::table header="Results">
                <x-slot name="header">
                    <x-tables::header-cell name="name">
                        Name
                    </x-tables::header-cell>
                    <!-- <x-tables::header-cell name="result">
                        Result
                    </x-tables::header-cell> -->
                </x-slot>
            </x-tables::table>
        </div>
    </x-tables::container>
</x-filament::page>