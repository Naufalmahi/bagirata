@php $sessionMembers = $session->members; @endphp
<div x-data="expenseForm(@js($initial))">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method($method)

        <div>
            <label class="mb-1 block text-sm font-semibold">Apaan ni pengeluarannya?</label>
            <input type="text" name="name" x-model="name" required maxlength="120" @input="refreshPreview()" placeholder="cth: Nasi goreng + es teh"
                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold">Kategori</label>
                <select name="category" x-model="category" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (\App\Enums\ExpenseCategory::cases() as $cat)
                        <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Nominal (Rp)</label>
                <input type="number" name="amount" x-model.number="amount" min="1" step="1" required 
                    @input="refreshPreview()" placeholder="cth: 150000"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('amount')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-semibold">Diskon</label>
                <select name="discount_type" x-model="discount_type" @change="refreshPreview()" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (\App\Enums\DiscountType::cases() as $dt)
                        <option value="{{ $dt->value }}">{{ $dt->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">
                    <span x-show="discount_type === 'fixed'">Nilai diskon (Rp)</span>
                    <span x-show="discount_type === 'percent'" x-cloak>Diskon (%)</span>
                </label>
                <input type="number" name="discount_value" x-model.number="discount_value" min="0" step="1"
                    @input="refreshPreview()" placeholder="0"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('discount_value')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Service (%)</label>
                <input type="number" name="service_rate" x-model.number="service_rate" min="0" max="100" step="1"
                    @input="refreshPreview()" placeholder="0"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold">Pajak (%)</label>
                <input type="number" name="tax_rate" x-model.number="tax_rate" min="0" max="100" step="1"
                    @input="refreshPreview()" placeholder="0"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Siapa yang bayar duluan?</label>
                <select name="paid_by_user_id" x-model.number="paid_by" @change="refreshPreview()" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach ($sessionMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} {{ $member->id === auth()->id() ? '(lu)' : '' }}</option>
                    @endforeach
                </select>
                @error('paid_by_user_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold">Cara ngitung bagian</label>
            <div class="flex flex-wrap gap-2">
                @foreach (\App\Enums\SplitType::cases() as $st)
                    <label class="cursor-pointer">
                        <input type="radio" name="split_type" value="{{ $st->value }}" x-model="split_type" @change="refreshPreview()" class="peer sr-only">
                        <span class="inline-block rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700">
                            {{ $st->label() }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold">Siapa yang ikut nanggung? (&ge; 2 orang)</label>
            @error('participant_ids')<p class="mb-2 text-sm text-rose-600">{{ $message }}</p>@enderror

            <div class="space-y-2">
                <template x-for="m in members" :key="m.id">
                    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5"
                        :class="participants[m.id] ? 'border-indigo-300 bg-indigo-50/40' : ''">
                        <label class="flex min-w-0 flex-1 cursor-pointer items-center gap-2 text-sm">
                            <input type="checkbox" :checked="!!participants[m.id]"
                                @change="participants[m.id] = $event.target.checked; refreshPreview()"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="truncate font-medium" x-text="m.name"></span>
                        </label>

                        <template x-if="split_type === 'custom'">
                            <div class="flex items-center gap-1.5 text-sm">
                                <span class="text-xs text-slate-400">Rp</span>
                                <input type="number" min="0" step="1" placeholder="0"
                                    x-model="custom_amounts[m.id]" @input="refreshPreview()"
                                    class="w-28 rounded-lg border-slate-200 text-right focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </template>

                        <template x-if="split_type === 'percentage'">
                            <div class="flex items-center gap-1.5 text-sm">
                                <input type="number" min="0" max="100" step="1" placeholder="0"
                                    x-model="percentages[m.id]" @input="refreshPreview()"
                                    class="w-20 rounded-lg border-slate-200 text-right focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="text-xs text-slate-400">%</span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <p class="mt-2 text-xs text-slate-400" x-show="split_type === 'custom'" x-cloak>
                Total nominal harus pas sama grand total nanti. 
            </p>
            <p class="mt-2 text-xs text-slate-400" x-show="split_type === 'percentage'" x-cloak>
                Total persen harus pas 100.
            </p>
            @error('custom_amounts')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            @error('percentages')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        {{-- submit payload: participant ids + per-split values --}}
        <template x-for="id in participantIds()" :key="'part-' + id">
            <input type="hidden" name="participant_ids[]" :value="id">
        </template>
        <template x-for="id in participantIds()" :key="'custom-' + id" x-if="split_type === 'custom'">
            <input type="hidden" :name="'custom_amounts[' + id + ']'" :value="custom_amounts[id] ?? 0">
        </template>
        <template x-for="id in participantIds()" :key="'pct-' + id" x-if="split_type === 'percentage'">
            <input type="hidden" :name="'percentages[' + id + ']'" :value="percentages[id] ?? 0">
        </template>

        {{-- Preview --}}
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-sm font-semibold text-slate-700">Preview hitungan</p>
            <p class="mt-1 text-xs text-slate-400" x-show="!loading && !preview && !error">Isi nominal + pilih peserta dulu.</p>
            <p x-show="loading" class="mt-1 text-sm text-slate-500">Ngitung...</p>
            <p x-show="error" x-text="error" class="mt-1 text-sm font-medium text-rose-600"></p>

            <template x-if="preview">
                <div>
                    <div class="mt-2 space-y-0.5 text-sm text-slate-600">
                        <p x-show="preview.discount_amount > 0">Diskon: -<span x-text="preview.discount_amount.toLocaleString('id-ID')"></span></p>
                        <p x-show="preview.service_amount > 0">Service: <span x-text="preview.service_amount.toLocaleString('id-ID')"></span></p>
                        <p x-show="preview.tax_amount > 0">Pajak: <span x-text="preview.tax_amount.toLocaleString('id-ID')"></span></p>
                        <p class="font-bold text-slate-900">Grand total: <span x-text="preview.grand_total.toLocaleString('id-ID')"></span></p>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <template x-for="split in preview.splits" :key="split.user_id">
                            <span class="rounded-full bg-white px-2.5 py-1 text-xs text-slate-600 border border-slate-200">
                                <span x-text="split.name"></span> <strong x-text="split.share.toLocaleString('id-ID')"></strong>
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold">Catatan <span class="font-normal text-slate-400">(opsional)</span></label>
            <textarea name="note" rows="2" maxlength="500" x-model="note" placeholder="cth: minta gak pake bawang"
                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('note')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold">Struk / bukti <span class="font-normal text-slate-400">(opsional, max 2MB)</span></label>
            <input type="file" name="receipt_photo" accept="image/jpeg,image/png,image/webp"
                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
            @error('receipt_photo')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            @if (!empty($expense->receipt_photo))
                <label class="mt-2 flex items-center gap-2 text-sm text-slate-500">
                    <input type="checkbox" name="remove_receipt" value="1" class="rounded border-slate-300 text-indigo-600">
                    Hapus struk yang sekarang
                </label>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <button class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">{{ $buttonLabel }}</button>
            <a href="{{ route('nongkrong.show', $session) }}" class="text-sm font-semibold text-slate-500 hover:underline">Batal</a>
        </div>
    </form>
</div>