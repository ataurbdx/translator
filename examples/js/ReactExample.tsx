import React, { useState } from 'react';
import { TranslatorClient } from '../../packages/js/src/index';
import { TranslatorProvider, useTranslator } from '../../packages/js/src/react';

// 1. Initialize client pointing to your Laravel backend
const client = new TranslatorClient({
    baseUrl: 'https://api.yourdomain.com',
    locale: 'bn',
    fallbackLocale: 'en',
});

// 2. Sample Component using Translator hook
function ProductCard() {
    const { t, formatDigits, formatNumber, loaded } = useTranslator();

    if (!loaded) {
        return <div>Loading translations...</div>;
    }

    const price = 1250000;
    const year = 2026;

    return (
        <div style={{ border: '1px solid #ddd', padding: '16px', borderRadius: '8px', maxWidth: '300px' }}>
            <h2>{t('product.sample_title', 'Gaming Laptop')}</h2>
            
            <p>
                <strong>Year:</strong> {formatDigits(year)} {/* ২০২৬ */}
            </p>
            <p>
                <strong>Price:</strong> ৳{formatNumber(price)} {/* ১২,৫০,০০০ */}
            </p>

            <button style={{ background: '#0070f3', color: '#fff', padding: '8px 16px', border: 'none', borderRadius: '4px' }}>
                {t('button.add_to_cart', 'Add to Cart')} {/* কার্টে যোগ করুন */}
            </button>
        </div>
    );
}

// 3. Main App Wrapping with Provider
export default function App() {
    return (
        <TranslatorProvider value={client}>
            <div style={{ padding: '24px' }}>
                <h1>Translator - React / Next.js Demo</h1>
                <ProductCard />
            </div>
        </TranslatorProvider>
    );
}
