<x-filament::page>
    <h5 style="font-weight:bold">Statistics</h5>

    <x-tables::container>
        <div class="overflow-y-auto relative rounded-t-xl">
            <x-tables::table header="Results">
                <x-slot name="header">
                    <x-tables::header-cell name="client">
                        Client
                    </x-tables::header-cell>
                    <x-tables::header-cell name="status">
                        Status
                    </x-tables::header-cell>
                </x-slot>
                @php
                $clients = $record->clients;
                @endphp
                @foreach($clients as $client)
                <x-tables::row>
                    <x-tables::cell class="px-4 py-3">
                        <a href="{{'/admin/clients/'.$client['id']}}" target="_blank" style="color: blue;">
                            {{ $client['tg_username']}}
                        </a>
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        @if($client['claim']['status'] == \App\Enums\ClaimStatus::Apply)
                        Apply
                        @elseif($client['claim']['status'] == \App\Enums\ClaimStatus::Deny)
                        Deny
                        @elseif($client['claim']['status'] == \App\Enums\ClaimStatus::Pending)
                        Pending
                        @endif
                    </x-tables::cell>
                </x-tables::row>
                @endforeach
            </x-tables::table>

        </div>
    </x-tables::container>

    <hr>
    <h5 style="font-weight:bold">Summary</h5>

    <x-tables::container>
        <div class="overflow-y-auto relative rounded-t-xl">
            <x-tables::table header="Results">
                <x-slot name="header">
                    <x-tables::header-cell name="client">
                        Total Client
                    </x-tables::header-cell>
                    <x-tables::header-cell name="apply">
                        Total Apply
                    </x-tables::header-cell>
                    <x-tables::header-cell name="deny">
                        Total Deny
                    </x-tables::header-cell>
                    <x-tables::header-cell name="pending">
                        Total Pending
                    </x-tables::header-cell>
                </x-slot>
                @php
                $data = array();
                $total_clients = $record->clients()->count();
                $data['total_clients'] = $total_clients;
                $total_apply = $record->clients()->wherePivot('status',\App\Enums\ClaimStatus::Apply)->count();
                $data['total_apply'] = $total_apply;
                $total_deny = $record->clients()->wherePivot('status',\App\Enums\ClaimStatus::Deny)->count();
                $data['total_deny'] = $total_deny;
                $total_pending = $record->clients()->wherePivot('status',\App\Enums\ClaimStatus::Pending)->count();
                $data['total_pending'] = $total_pending;
                @endphp
                @foreach(array($data) as $data)
                <x-tables::row>
                    <x-tables::cell class="px-4 py-3">
                        {{ $data['total_clients']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{ $data['total_apply']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{ $data['total_deny']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{ $data['total_pending']}}
                    </x-tables::cell>

                </x-tables::row>
                @endforeach
            </x-tables::table>
        </div>
    </x-tables::container>

</x-filament::page>