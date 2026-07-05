import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'dart:io';

class ApiService {
  static const String baseUrl = 'http://192.168.56.1:8000/api';

  static Future<void> _saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  static Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  static Future<Map<String, String>> _authHeaders() async {
    final token = await getToken();
    return {
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  static Map<String, dynamic> _safeDecode(http.Response response) {
    try {
      return json.decode(response.body);
    } catch (_) {
      return {
        'status': 'error',
        'message': 'Server error (kode ${response.statusCode})',
      };
    }
  }

  static Future<Map<String, dynamic>> login({
    required String nip,
    required String password,
  }) async {
    try {
      var response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {'Accept': 'application/json'},
        body: {'nip': nip, 'password': password},
      );
      var result = _safeDecode(response);
      if (response.statusCode == 200 && result['status'] == 'success') {
        await _saveToken(result['data']['token']);
      }
      return result;
    } catch (e) {
      return {
        'status': 'error',
        'message': 'Gagal terhubung ke server',
        'detail': e.toString(),
      };
    }
  }

  static Future<Map<String, dynamic>> logout() async {
    try {
      var response = await http.post(
        Uri.parse('$baseUrl/logout'),
        headers: await _authHeaders(),
      );
      await clearToken();
      return _safeDecode(response);
    } catch (e) {
      await clearToken();
      return {'status': 'error', 'message': 'Gagal logout'};
    }
  }

  static Future<Map<String, dynamic>> clockIn({
    required File photoFile,
    required double latitude,
    required double longitude,
  }) async {
    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('$baseUrl/attendances/clock-in'),
      );
      request.headers.addAll(await _authHeaders());
      request.fields['latitude'] = latitude.toString();
      request.fields['longitude'] = longitude.toString();
      request.files.add(
        await http.MultipartFile.fromPath('photo', photoFile.path),
      );
      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);
      return _safeDecode(response);
    } catch (e) {
      return {
        'status': 'error',
        'message': 'Gagal terhubung ke server',
        'detail': e.toString(),
      };
    }
  }

  static Future<Map<String, dynamic>> clockOut({
    required File photoFile,
    required double latitude,
    required double longitude,
  }) async {
    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('$baseUrl/attendances/clock-out'),
      );
      request.headers.addAll(await _authHeaders());
      request.fields['latitude'] = latitude.toString();
      request.fields['longitude'] = longitude.toString();
      request.files.add(
        await http.MultipartFile.fromPath('photo', photoFile.path),
      );
      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);
      return _safeDecode(response);
    } catch (e) {
      return {
        'status': 'error',
        'message': 'Gagal terhubung ke server',
        'detail': e.toString(),
      };
    }
  }

  static Future<Map<String, dynamic>> getHistory() async {
    try {
      var response = await http.get(
        Uri.parse('$baseUrl/attendances/history'),
        headers: await _authHeaders(),
      );
      return _safeDecode(response);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal memuat riwayat'};
    }
  }
}
