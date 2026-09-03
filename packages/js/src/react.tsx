import { useState, useEffect, useContext, createContext } from 'react';
import { TranslatorClient } from './index';

const TranslatorContext = createContext<TranslatorClient | null>(null);

export const TranslatorProvider = TranslatorContext.Provider;

/**
 * React hook for consuming Translator in Next.js / React / MERN apps
 */
export function useTranslator() {
    const client = useContext(TranslatorContext);

    if (!client) {
        throw new Error('useTranslator must be used within a TranslatorProvider');
    }

    const [loaded, setLoaded] = useState(false);

    useEffect(() => {
        client.loadStatic().then(() => setLoaded(true));
    }, [client]);

    return {
        loaded,
        t: (key: string, fallback?: string) => client.t(key, fallback),
        formatDigits: (val: string | number) => client.formatDigits(val),
        formatNumber: (val: number, decimals?: number) => client.formatNumber(val, decimals),
    };
}
