/**
 * Design token BagiRata.
 *
 * Warna didefinisikan sebagai CSS variable RGB triplet supaya:
 *  1. bisa dipakai dengan opacity modifier (`bg-surface/60`)
 *  2. bisa di-flip sekali jalan lewat kelas `dark` di <html>
 *
 * Konvensi nama:
 *   surface / raised / sunken - lapisan latar
 *   line                     - garis pemisah (line.DEFAULT, line.strong)
 *   ink                      - teks (ink.DEFAULT, ink.muted, ink.subtle)
 *   brand                    - aksi utama indigo
 *   success/warning/danger/info - status semantik
 *
 * Tiap status punya pasangan `-soft` (latar) dan `-text` (teks di atasnya)
 * supaya kontrasnya terkontrol di light maupun dark.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    // WAJIB: Tailwind v3 memangkas isi `@layer components` yang tidak ditemukan
    // di hasil content scan. Tanpa safelist ini, seluruh kelas design system
    // (.btn, .card, .alert, ...) dibuang dari build sampai ada view yang
    // memakainya — dan `public/build` di-gitignore, jadi deploy akan bangun
    // CSS yang bolong. Daftar di bawah adalah satu-satunya sumber kebenaran;
    // tambah kelas component baru di app.css => tambah di sini juga.
    safelist: [
        // tombol
        'btn', 'btn-primary', 'btn-secondary', 'btn-ghost', 'btn-danger',
        'btn-sm', 'btn-lg', 'btn-icon',
        // permukaan
        'card', 'card-hover', 'card-sunken', 'brand-mark', 'avatar',
        // form
        'label', 'input', 'input-sm', 'select', 'select-sm', 'textarea', 'checkbox',
        'file-input', 'field-error', 'hint', 'chip', 'row-toggle',
        // navigasi
        'nav-link', 'nav-link-active', 'back-link',
        // tipografi
        'page-title', 'section-title', 'muted',
        // badge
        'badge', 'badge-neutral', 'badge-brand', 'badge-success',
        'badge-warning', 'badge-danger', 'badge-info',
        // statistik
        'stat', 'stat-label', 'stat-value', 'stat-neutral', 'stat-danger',
        'stat-success', 'stat-warning', 'stat-brand',
        // alert
        'alert', 'alert-success', 'alert-danger', 'alert-warning', 'alert-info',
        // daftar & tabel
        'divide-list', 'empty', 'empty-panel', 'list-row', 'table-wrap', 'table', 'money',
    ],
    theme: {
        // Sengaja `extend`, bukan `theme`: palette bawaan Tailwind (slate-*, rose-*,
        // text-white, rounded-xl, ...) tetap harus ada selama migrasi view masih
        // bertahap. Token di bawah sifatnya aditif, bukan pengganti.
        extend: {
            colors: {
                transparent: 'transparent',
                current: 'currentColor',
                inherit: 'inherit',

                surface: 'rgb(var(--surface) / <alpha-value>)',
                raised: 'rgb(var(--raised) / <alpha-value>)',
                sunken: 'rgb(var(--sunken) / <alpha-value>)',

                line: {
                    DEFAULT: 'rgb(var(--line) / <alpha-value>)',
                    strong: 'rgb(var(--line-strong) / <alpha-value>)',
                },

                ink: {
                    DEFAULT: 'rgb(var(--ink) / <alpha-value>)',
                    muted: 'rgb(var(--ink-muted) / <alpha-value>)',
                    subtle: 'rgb(var(--ink-subtle) / <alpha-value>)',
                },

                brand: {
                    DEFAULT: 'rgb(var(--brand) / <alpha-value>)',
                    hover: 'rgb(var(--brand-hover) / <alpha-value>)',
                    soft: 'rgb(var(--brand-soft) / <alpha-value>)',
                    text: 'rgb(var(--brand-text) / <alpha-value>)',
                },

                success: {
                    DEFAULT: 'rgb(var(--success) / <alpha-value>)',
                    soft: 'rgb(var(--success-soft) / <alpha-value>)',
                    text: 'rgb(var(--success-text) / <alpha-value>)',
                },
                warning: {
                    DEFAULT: 'rgb(var(--warning) / <alpha-value>)',
                    soft: 'rgb(var(--warning-soft) / <alpha-value>)',
                    text: 'rgb(var(--warning-text) / <alpha-value>)',
                },
                danger: {
                    DEFAULT: 'rgb(var(--danger) / <alpha-value>)',
                    soft: 'rgb(var(--danger-soft) / <alpha-value>)',
                    text: 'rgb(var(--danger-text) / <alpha-value>)',
                },
                info: {
                    DEFAULT: 'rgb(var(--info) / <alpha-value>)',
                    soft: 'rgb(var(--info-soft) / <alpha-value>)',
                    text: 'rgb(var(--info-text) / <alpha-value>)',
                },
            },

            borderRadius: {
                card: '0.875rem',
                control: '0.625rem',
            },

            boxShadow: {
                card: '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.06)',
                'card-hover': '0 4px 12px -2px rgb(15 23 42 / 0.10), 0 2px 6px -2px rgb(15 23 42 / 0.06)',
                pop: '0 12px 32px -8px rgb(15 23 42 / 0.18)',
            },

            keyframes: {
                'fade-in': {
                    from: { opacity: '0', transform: 'translateY(4px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
                'scale-in': {
                    from: { opacity: '0', transform: 'scale(0.97)' },
                    to: { opacity: '1', transform: 'scale(1)' },
                },
            },

            animation: {
                'fade-in': 'fade-in 0.18s ease-out',
                'scale-in': 'scale-in 0.14s ease-out',
            },
        },

        fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
            mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Consolas', 'monospace'],
        },
    },
    plugins: [],
};
