<x-filament::page>
    <h5 style="font-weight:bold">Client</h5>
    <x-tables::container>
        <div class="overflow-y-auto relative rounded-t-xl">
            <x-tables::table header="Results">
                <x-slot name="header">
                    <x-tables::header-cell name="username">
                        Username
                    </x-tables::header-cell>
                    <x-tables::header-cell name="geo">
                        Geo
                    </x-tables::header-cell>
                    <x-tables::header-cell name="prime">
                        Prime
                    </x-tables::header-cell>
                    <x-tables::header-cell name="status">
                        Status
                    </x-tables::header-cell>
                    <x-tables::header-cell name="joined">
                        Joined
                    </x-tables::header-cell>
                    <x-tables::header-cell name="interestes">
                        Interestes
                    </x-tables::header-cell>
                    <x-tables::header-cell name="orders">
                        Orders
                    </x-tables::header-cell>
                </x-slot>
                @php

                $client = $record;
                $count_order = $client->orders()->count();
                @endphp
                <x-tables::row>
                    <x-tables::cell class="px-4 py-3">

                        <a href="http://t.me/{{$client['tg_username']}}" target="_blank" style="color: blueviolet;">
                            {{$client['tg_username']}}
                        </a>
                    </x-tables::cell>

                    <x-tables::cell class="px-4 py-3 text-bold">
                        {{$client['geo']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">

                        @if($client['prime'] == 1)
                        Yes
                        @else
                        No
                        @endif
                    </x-tables::cell>

                    <x-tables::cell class="px-4 py-3">

                        @if($client['status'] == \App\Enums\ClientStatus::Pending)
                        Pending
                        @elseif($client['status'] == \App\Enums\ClientStatus::Deny)
                        Denied
                        @elseif($client['status'] == \App\Enums\ClientStatus::Approve)
                        Approved
                        @endif
                    </x-tables::cell>

                    <x-tables::cell class="px-4 py-3">
                        {{\Carbon\Carbon::parse($client['created_at'])->toFormattedDateString()}}
                    </x-tables::cell>

                    <x-tables::cell class="px-4 py-3 font-bold">
                        {{implode(" , ",$client['interestes'])}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{$count_order}}
                    </x-tables::cell>

                </x-tables::row>
            </x-tables::table>
        </div>
    </x-tables::container>




    <hr>
    <h5 style="font-weight:bold">Statistics</h5>

    <x-tables::container>
        <div class="overflow-y-auto relative rounded-t-xl">
            <x-tables::table header="Results">
                <x-slot name="header">
                    <x-tables::header-cell>
                        Total Claims
                    </x-tables::header-cell>
                    <x-tables::header-cell name="campaign">
                        Deny Claims
                    </x-tables::header-cell>
                    <x-tables::header-cell name="status">
                        Apply Claims
                    </x-tables::header-cell>
                    <x-tables::header-cell name="status">
                        Pending Claims
                    </x-tables::header-cell>
                </x-slot>
                @php
                $history = array();
                $claims = \App\Models\CampaignClient::where('client_id',$record->id)->get();
                $total_claims = $claims->count();
                $history['total_claims'] = $total_claims;
                $deny_claims = $claims->filter(function ($value, $key) {
                return $value->status== \App\Enums\ClaimStatus::Deny;
                })->count();
                $history['deny_claims'] = $deny_claims;
                $apply_claims = $claims->filter(function ($value, $key) {
                return $value->status== \App\Enums\ClaimStatus::Apply;
                })->count();
                $history['apply_claims'] = $apply_claims;
                $pending_claims = $claims->filter(function ($value, $key) {
                return $value->status== \App\Enums\ClaimStatus::Pending;
                })->count();
                $history['pending_claims'] = $pending_claims;
                $hh = array();
                $hh['history'] = $history;
                @endphp
                @foreach($hh as $history)
                <x-tables::row>
                    <x-tables::cell class="px-4 py-3">
                        {{ $history['total_claims']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{ $history['deny_claims']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{ $history['apply_claims']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{ $history['pending_claims']}}
                    </x-tables::cell>
                </x-tables::row>
                @endforeach
            </x-tables::table>
        </div>
    </x-tables::container>

    <hr>
    <h5 style="font-weight:bold">target chats</h5>
    <x-tables::container>
        <div class="overflow-y-auto relative rounded-t-xl">
            <x-tables::table header="Results">
                <x-slot name="header">
                    <x-tables::header-cell>
                        ID
                    </x-tables::header-cell>
                    <x-tables::header-cell name="campaign">
                        Title
                    </x-tables::header-cell>
                    <x-tables::header-cell name="status">
                        Username
                    </x-tables::header-cell>
                </x-slot>
                @php
                $claims = \App\Models\CampaignClient::where('client_id',$record->id)->pluck('claim_target_chat_id')->unique()->all();
                $bot = new SergiX44\Nutgram\Nutgram(config('nutgram.token'));
                $target_chats = array();
                foreach($claims as $claim){
                $get_chat = $bot->getChat((int)$claim);
                $chat = array();
                $chat['id'] = $get_chat->id;
                $chat['title'] = $get_chat->title;
                $chat['username'] = $get_chat->username;
                $target_chats[$claim] = $chat;
                }
                @endphp
                @foreach($target_chats as $chats)
                <x-tables::row>
                    <x-tables::cell class="px-4 py-3">
                        {{$chats['id']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">
                        {{$chats['title']}}
                    </x-tables::cell>
                    <x-tables::cell class="px-4 py-3">

                        <a href="http://t.me/{{$chats['username']}}" target="_blank" style="color: blue;">
                            {{$chats['username']}}
                        </a>
                    </x-tables::cell>
                </x-tables::row>
                @endforeach
            </x-tables::table>
        </div>
    </x-tables::container>
</x-filament::page>