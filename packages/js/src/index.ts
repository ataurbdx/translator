export interface TranslatorConfig {
    baseUrl: string;
    locale: string;
    fallbackLocale?: string;
}

export class TranslatorClient {
    private baseUrl: string;
    private locale: string;
    private fallbackLocale: string;
    private cache: Record<string, string> = {};

    constructor(config: TranslatorConfig) {
        this.baseUrl = config.baseUrl.replace(/\/$/, '');
        this.locale = config.locale || 'en';
        this.fallbackLocale = config.fallbackLocale || 'en';
    }

    /**
     * Fetch static UI translations from Laravel backend
     */
    async loadStatic(group?: string): Promise<Record<string, string>> {
        const url = `${this.baseUrl}/api/v1/translator/static?locale=${this.locale}` + (group ? `&group=${group}` : '');
        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const json = await res.json();
                this.cache = { ...this.cache, ...json.translations };
                return this.cache;
            }
        } catch (e) {
            console.error('[Translator] Failed to load translations', e);
        }
        return this.cache;
    }

    /**
     * Unified translate method supporting text, digits, number
     */
    translate(
        value: string | number,
        options?: { type?: 'text' | 'digits' | 'number'; locale?: string; fallback?: string; decimals?: number }
    ): string {
        const type = options?.type || 'text';
        const locale = options?.locale || this.locale;

        if (type === 'digits') {
            return this.formatDigits(value, locale);
        }
        if (type === 'number') {
            return this.formatNumber(Number(value) || 0, options?.decimals || 0, locale);
        }
        return this.cache[String(value)] || options?.fallback || String(value);
    }

    /**
     * Shorthand alias
     */
    t(key: string, fallback?: string): string {
        return this.translate(key, { fallback });
    }

    /**
     * Localized digits formatting (e.g. 2026 -> ২০২৬)
     */
    formatDigits(value: string | number, locale: string = this.locale): string {
        const str = String(value);
        if (locale === 'bn') {
            const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return str.replace(/[0-9]/g, d => bnDigits[parseInt(d, 10)]);
        }
        if (locale === 'ar') {
            const arDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return str.replace(/[0-9]/g, d => arDigits[parseInt(d, 10)]);
        }
        return str;
    }

    /**
     * Localized number formatting with South Asian grouping (Lakh/Crore)
     */
    formatNumber(value: number, decimals: number = 0, locale: string = this.locale): string {
        const isSouthAsian = locale === 'bn' || locale === 'in';
        const numStr = Math.abs(value).toFixed(decimals);
        const [intPart, decPart] = numStr.split('.');

        let formattedInt = intPart;
        if (isSouthAsian && intPart.length > 3) {
            const lastThree = intPart.slice(-3);
            const rest = intPart.slice(0, -3);
            const chunks = [];
            for (let i = rest.length; i > 0; i -= 2) {
                chunks.unshift(rest.slice(Math.max(0, i - 2), i));
            }
            formattedInt = chunks.join(',') + ',' + lastThree;
        } else {
            formattedInt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        const signed = (value < 0 ? '-' : '') + formattedInt + (decPart ? '.' + decPart : '');
        return this.formatDigits(signed, locale);
    }
}
