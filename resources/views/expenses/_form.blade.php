@php $sessionMembers = $session->members; @endphp
<div x-data="expenseForm(@js($initial))">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method($method)

        <div>
            <label class="label" for="exp-name">Apaan ni pengeluarannya?</label>
            <input class="input" id="exp-name" type="text" name="name" x-model="name" required maxlength="120"
                @input="refreshPreview()" placeholder="cth: Nasi goreng + es teh">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="exp-category">Kategori</label>
                <select class="select" id="exp-category" name="category" x-model="category">
                    @foreach (\App\Enums\ExpenseCategory::cases() as $cat)
                        <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="exp-amount">Nominal (Rp)</label>
                <input class="input" id="exp-amount" type="number" name="amount" x-model.number="amount" min="1" step="1" required
                    @input="refreshPreview()" placeholder="cth: 150000">
                @error('amount')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label" for="exp-discount-type">Diskon</label>
                <select class="select" id="exp-discount-type" name="discount_type" x-model="discount_type" @change="refreshPreview()">
                    @foreach (\App\Enums\DiscountType::cases() as $dt)
                        <option value="{{ $dt->value }}">{{ $dt->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="exp-discount-value">
                    <span x-show="discount_type === 'fixed'">Nilai diskon (Rp)</span>
                    <span x-show="discount_type === 'percent'" x-cloak>Diskon (%)</span>
                </label>
                <input class="input" id="exp-discount-value" type="number" name="discount_value" x-model.number="discount_value" min="0" step="1"
                    @input="refreshPreview()" placeholder="0">
                @error('discount_value')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="exp-service-rate">Service (%)</label>
                <input class="input" id="exp-service-rate" type="number" name="service_rate" x-model.number="service_rate" min="0" max="100" step="1"
                    @input="refreshPreview()" placeholder="0">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="exp-tax-rate">Pajak (%)</label>
                <input class="input" id="exp-tax-rate" type="number" name="tax_rate" x-model.number="tax_rate" min="0" max="100" step="1"
                    @input="refreshPreview()" placeholder="0">
            </div>
            <div>
                <label class="label" for="exp-paid-by">Siapa yang bayar duluan?</label>
                <select class="select" id="exp-paid-by" name="paid_by_user_id" x-model.number="paid_by" @change="refreshPreview()">
                    @foreach ($sessionMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} {{ $member->id === auth()->id() ? '(lu)' : '' }}</option>
                    @endforeach
                </select>
                @error('paid_by_user_id')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <span class="label">Cara ngitung bagian</span>
            <div class="flex flex-wrap gap-2">
                @foreach (\App\Enums\SplitType::cases() as $st)
                    <label class="cursor-pointer">
                        <input type="radio" name="split_type" value="{{ $st->value }}" x-model="split_type"
                            @change="refreshPreview()" class="peer sr-only">
                        <span class="chip">{{ $st->label() }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <span class="label">Siapa yang ikut nanggung? (&ge; 2 orang)</span>
            @error('participant_ids')<p class="field-error mb-2">{{ $message }}</p>@enderror

            <div class="space-y-2">
                <template x-for="m in members" :key="m.id">
                    <div class="row-toggle" :class="participants[m.id] ? 'border-brand bg-brand-soft/40' : ''">
                        <label class="flex min-w-0 flex-1 cursor-pointer items-center gap-2 text-sm">
                            <input type="checkbox" class="checkbox" :checked="!!participants[m.id]"
                                @change="participants[m.id] = $event.target.checked; refreshPreview()">
                            <span class="truncate font-medium text-ink" x-text="m.name"></span>
                        </label>

                        <template x-if="split_type === 'custom'">
                            <div class="flex items-center gap-1.5 text-sm">
                                <span class="text-xs text-ink-subtle">Rp</span>
                                <input type="number" min="0" step="1" placeholder="0" class="input input-sm w-28 text-right"
                                    x-model="custom_amounts[m.id]" @input="refreshPreview()">
                            </div>
                        </template>

                        <template x-if="split_type === 'percentage'">
                            <div class="flex items-center gap-1.5 text-sm">
                                <input type="number" min="0" max="100" step="1" placeholder="0" class="input input-sm w-20 text-right"
                                    x-model="percentages[m.id]" @input="refreshPreview()">
                                <span class="text-xs text-ink-subtle">%</span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <p class="hint" x-show="split_type === 'custom'" x-cloak>
                Total nominal harus pas sama grand total nanti.
            </p>
            <p class="hint" x-show="split_type === 'percentage'" x-cloak>
                Total persen harus pas 100.
            </p>
            @error('custom_amounts')<p class="field-error">{{ $message }}</p>@enderror
            @error('percentages')<p class="field-error">{{ $message }}</p>@enderror
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
        <div class="card-sunken">
            <p class="text-sm font-semibold text-ink">Preview hitungan</p>
            <p class="hint" x-show="!loading && !preview && !error">Isi nominal + pilih peserta dulu.</p>
            <p x-show="loading" class="mt-1 text-sm text-ink-muted">Ngitung...</p>
            <p x-show="error" x-text="error" class="field-error font-medium"></p>

            <template x-if="preview">
                <div>
                    <div class="mt-2 space-y-0.5 text-sm text-ink-muted">
                        <p x-show="preview.discount_amount > 0">Diskon: -<span x-text="preview.discount_amount.toLocaleString('id-ID')"></span></p>
                        <p x-show="preview.service_amount > 0">Service: <span x-text="preview.service_amount.toLocaleString('id-ID')"></span></p>
                        <p x-show="preview.tax_amount > 0">Pajak: <span x-text="preview.tax_amount.toLocaleString('id-ID')"></span></p>
                        <p class="font-bold text-ink">Grand total: <span x-text="preview.grand_total.toLocaleString('id-ID')"></span></p>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <template x-for="split in preview.splits" :key="split.user_id">
                            <span class="badge badge-neutral">
                                <span x-text="split.name"></span>
                                <strong class="money" x-text="split.share.toLocaleString('id-ID')"></strong>
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div>
            <label class="label" for="exp-note">Catatan <span class="font-normal text-ink-subtle">(opsional)</span></label>
            <textarea class="textarea" id="exp-note" name="note" rows="2" maxlength="500" x-model="note"
                placeholder="cth: minta gak pake bawang"></textarea>
            @error('note')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="label" for="exp-receipt">Struk / bukti <span class="font-normal text-ink-subtle">(opsional, max 2MB)</span></label>
            <input class="file-input" id="exp-receipt" type="file" name="receipt_photo" accept="image/jpeg,image/png,image/webp">
            @error('receipt_photo')<p class="field-error">{{ $message }}</p>@enderror
            @if (!empty($expense->receipt_photo))
                <label class="mt-2 flex items-center gap-2 text-sm text-ink-muted">
                    <input type="checkbox" name="remove_receipt" value="1" class="checkbox">
                    Hapus struk yang sekarang
                </label>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <button class="btn btn-primary btn-lg">{{ $buttonLabel }}</button>
            <a href="{{ route('nongkrong.show', $session) }}" class="text-sm font-semibold text-ink-muted hover:underline">Batal</a>
        </div>
    </form>
</div>
