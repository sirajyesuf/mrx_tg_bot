<x-filament::page>
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
                </x-slot>
                @php
                $history = array();
                $claims = \App\Models\CampaignClient::where('client_id',$record->id)->get();
                $total_claims = $claims->count();
                $history['total_claims'] = $total_claims;
                $deny_claims = $claims->filter(function ($value, $key) {
                return $value->status==0;
                })->count();
                $history['deny_claims'] = $deny_claims;
                $apply_claims = $claims->filter(function ($value, $key) {
                return $value->status==1;
                })->count();
                $history['apply_claims'] = $apply_claims;
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