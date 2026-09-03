import 'package:flutter/material.dart';
import '../../packages/flutter/lib/translator.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // 1. Initialize Translator pointing to your Laravel backend
  final translator = Translator.init(
    baseUrl: 'https://api.yourdomain.com',
    defaultLocale: 'bn',
  );

  // 2. Load cached translations immediately, then fetch fresh ones
  await translator.load();

  runApp(const TranslatorFlutterApp());
}

class TranslatorFlutterApp extends StatelessWidget {
  const TranslatorFlutterApp({super.key});

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
    final t = Translator.instance;

    const double price = 1250000;
    const int year = 2026;

    return Scaffold(
      appBar: AppBar(
        title: Text(t.translate('app.title', defaultText: 'Translator Flutter')),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Static Translation with Fallback
            Text(
              t.translate('welcome.message', defaultText: 'Welcome to our platform'),
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),

            // Localized Digits Formatting (2026 -> ২০২৬)
            Text('Year: ${t.formatDigits(year)}'),
            const SizedBox(height: 8),

            // South Asian Number Grouping (1250000 -> ১২,৫০,০০০)
            Text('Price: ৳${t.formatNumber(price)}'),
            const SizedBox(height: 20),

            // Button with Translated String
            ElevatedButton(
              onPressed: () {},
              child: Text(t.translate('button.add_to_cart', defaultText: 'Add to Cart')),
            ),
          ],
        ),
      ),
    );
  }
}
