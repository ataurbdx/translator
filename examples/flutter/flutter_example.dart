import 'package:flutter/material.dart';
import '../../packages/flutter/lib/translator_engine.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // 1. Initialize TranslatorEngine pointing to your Laravel backend
  final engine = TranslatorEngine.init(
    baseUrl: 'https://api.yourdomain.com',
    defaultLocale: 'bn',
  );

  // 2. Load cached translations immediately, then fetch fresh ones
  await engine.load();

  runApp(const TranslatorEngineFlutterApp());
}

class TranslatorEngineFlutterApp extends StatelessWidget {
  const TranslatorEngineFlutterApp({super.key});

  @override
  Widget build(BuildContext context) {
    return const MaterialApp(
      home: TranslationDemoScreen(),
    );
  }
}

class TranslationDemoScreen extends StatelessWidget {
  const TranslationDemoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final te = TranslatorEngine.instance;

    const double price = 1250000;
    const int year = 2026;

    return Scaffold(
      appBar: AppBar(
        title: Text(te.translate('app.title', defaultText: 'TranslatorEngine Flutter')),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Static Translation with Fallback
            Text(
              te.translate('welcome.message', defaultText: 'Welcome to our platform'),
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),

            // Localized Digits Formatting (2026 -> ২০২৬)
            Text('Year: ${te.formatDigits(year)}'),
            const SizedBox(height: 8),

            // South Asian Number Grouping (1250000 -> ১২,৫০,০০০)
            Text('Price: ৳${te.formatNumber(price)}'),
            const SizedBox(height: 20),

            // Button with Translated String
            ElevatedButton(
              onPressed: () {},
              child: Text(te.translate('button.add_to_cart', defaultText: 'Add to Cart')),
            ),
          ],
        ),
      ),
    );
  }
}
