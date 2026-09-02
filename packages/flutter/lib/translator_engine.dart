import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class TranslatorEngine {
  static TranslatorEngine? _instance;
  final String baseUrl;
  String currentLocale;
  Map<String, String> _translations = {};

  TranslatorEngine._internal({
    required this.baseUrl,
    required this.currentLocale,
  });

  static TranslatorEngine init({
    required String baseUrl,
    String defaultLocale = 'en',
  }) {
    _instance ??= TranslatorEngine._internal(
      baseUrl: baseUrl.replaceAll(RegExp(r'/$'), ''),
      currentLocale: defaultLocale,
    );
    return _instance!;
  }

  static TranslatorEngine get instance {
    if (_instance == null) {
      throw Exception('TranslatorEngine.init() must be called before using instance.');
    }
    return _instance!;
  }

  /// Initialize and load translations from Laravel API with local SharedPreferences cache
  Future<void> load({String? locale}) async {
    if (locale != null) currentLocale = locale;

    final prefs = await SharedPreferences.getInstance();
    final cacheKey = 'translator_engine_cache_$currentLocale';

    // 1. Load from local cache first for instant UI render
    final cachedData = prefs.getString(cacheKey);
    if (cachedData != null) {
      final Map<String, dynamic> decoded = jsonDecode(cachedData);
      _translations = decoded.map((k, v) => MapEntry(k, v.toString()));
    }

    // 2. Fetch fresh translations from Laravel TranslatorEngine API
    final url = Uri.parse('$baseUrl/api/v1/translator-engine/static?locale=$currentLocale');
    try {
      final res = await http.get(url, headers: {'Accept': 'application/json'});
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['translations'] != null) {
          final Map<String, dynamic> incoming = data['translations'];
          _translations = incoming.map((k, v) => MapEntry(k, v.toString()));
          await prefs.setString(cacheKey, jsonEncode(_translations));
        }
      }
    } catch (e) {
      // Offline fallback: keep cached data
    }
  }

  /// Unified translate method supporting text, digits, number
  String translate(
    dynamic value, {
    String type = 'text',
    String? defaultText,
    int decimals = 0,
  }) {
    if (type == 'digits') {
      return formatDigits(value);
    }
    if (type == 'number') {
      final numVal = num.tryParse(value.toString()) ?? 0;
      return formatNumber(numVal, decimals: decimals);
    }
    return _translations[value.toString()] ?? defaultText ?? value.toString();
  }

  /// Localize Western digits (e.g. '2026' -> '২০২৬')
  String formatDigits(dynamic value) {
    final str = value.toString();
    if (currentLocale == 'bn') {
      const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
      return str.replaceAllMapped(RegExp(r'[0-9]'), (match) {
        final d = int.parse(match.group(0)!);
        return bnDigits[d];
      });
    }
    return str;
  }

  /// Format numbers with South Asian grouping (Lakh/Crore)
  String formatNumber(num value, {int decimals = 0}) {
    final str = value.toStringAsFixed(decimals);
    final parts = str.split('.');
    final intPart = parts[0];
    final decPart = parts.length > 1 ? parts[1] : '';

    String formatted = intPart;
    if (currentLocale == 'bn' && intPart.length > 3) {
      final lastThree = intPart.substring(intPart.length - 3);
      var rest = intPart.substring(0, intPart.length - 3);
      final chunks = <String>[];
      while (rest.isNotEmpty) {
        if (rest.length >= 2) {
          chunks.insert(0, rest.substring(rest.length - 2));
          rest = rest.substring(0, rest.length - 2);
        } else {
          chunks.insert(0, rest);
          rest = '';
        }
      }
      formatted = '${chunks.join(',')},$lastThree';
    }

    if (decimals > 0 && decPart.isNotEmpty) {
      formatted = '$formatted.$decPart';
    }

    return formatDigits(formatted);
  }
}
