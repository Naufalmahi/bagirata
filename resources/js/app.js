import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.friendPicker = (apiBase, meId) => ({
    q: '',
    results: [],
    selected: [],
    loading: false,
    async search() {
        const term = this.q.trim();
        if (term.length < 2) {
            this.results = [];
            return;
        }

        this.loading = true;
        try {
            const res = await fetch(apiBase + '/users?q=' + encodeURIComponent(term), {
                headers: { Accept: 'application/json' },
            });
            const json = await res.json();
            const data = json.data || [];
            this.results = data.filter(
                (u) => u.id !== meId && !this.selected.some((s) => s.id === u.id)
            );
        } catch (error) {
            this.results = [];
        } finally {
            this.loading = false;
        }
    },
    add(user) {
        this.selected.push({ id: user.id, name: user.name });
        this.q = '';
        this.results = [];
    },
    remove(id) {
        this.selected = this.selected.filter((s) => s.id !== id);
    },
});

window.expenseForm = (initial) => ({
    name: initial.name ?? '',
    amount: initial.amount ?? '',
    category: initial.category ?? 'makan',
    discount_type: initial.discount_type ?? 'fixed',
    discount_value: initial.discount_value ?? '',
    service_rate: initial.service_rate ?? '',
    tax_rate: initial.tax_rate ?? '',
    split_type: initial.split_type ?? 'equal',
    paid_by: initial.paid_by ?? null,
    note: initial.note ?? '',
    members: initial.members ?? [],
    participants: initial.participants ?? {},
    custom_amounts: initial.custom_amounts ?? {},
    percentages: initial.percentages ?? {},
    preview: null,
    loading: false,
    error: '',
    participantIds() {
        return this.members.map((m) => m.id).filter((id) => this.participants[id]);
    },
    splitTotal() {
        if (this.split_type === 'custom') {
            return Object.values(this.custom_amounts).reduce((a, b) => a + (Number(b) || 0), 0);
        }
        if (this.split_type === 'percentage') {
            return Object.values(this.percentages).reduce((a, b) => a + (Number(b) || 0), 0);
        }
        return null;
    },
    async refreshPreview() {
        if (!this.amount || this.participantIds().length < 2 || !this.paid_by) {
            this.preview = null;
            return;
        }

        this.loading = true;
        this.error = '';
        this.preview = null;

        const payload = {
            name: 'preview',
            amount: Number(this.amount) || 0,
            paid_by_user_id: this.paid_by,
            category: this.category,
            discount_type: this.discount_type,
            discount_value: Number(this.discount_value) || 0,
            service_rate: Number(this.service_rate) || 0,
            tax_rate: Number(this.tax_rate) || 0,
            split_type: this.split_type,
            participant_ids: this.participantIds(),
        };
        if (this.split_type === 'custom') {
            payload.custom_amounts = { ...this.custom_amounts };
        }
        if (this.split_type === 'percentage') {
            payload.percentages = { ...this.percentages };
        }

        try {
            const res = await fetch(initial.preview_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });
            const json = await res.json();
            if (!res.ok) {
                const first = json.errors
                    ? Object.values(json.errors).flat().join(' · ')
                    : json.message;
                throw new Error(first || 'Cek lagi datanya yaa.');
            }
            this.preview = json.data;
        } catch (error) {
            this.error = error.message;
        } finally {
            this.loading = false;
        }
    },
});

Alpine.start();