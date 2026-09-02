import { useState, useEffect, useContext, createContext } from 'react';
import { TranslatorEngineClient } from './index';

const TranslatorEngineContext = createContext<TranslatorEngineClient | null>(null);

export const TranslatorEngineProvider = TranslatorEngineContext.Provider;

/**
 * React hook for consuming TranslatorEngine in Next.js / React / MERN apps
 */
export function useTranslatorEngine() {
    const client = useContext(TranslatorEngineContext);

    if (!client) {
        throw new Error('useTranslatorEngine must be used within a TranslatorEngineProvider');
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
