import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:geolocator/geolocator.dart';
import 'dart:io';
import '../api_service.dart';
import '../main.dart';

class ClockInPage extends StatefulWidget {
  final Map<String, dynamic> employeeData;
  final bool isClockIn;

  const ClockInPage({
    Key? key,
    required this.employeeData,
    this.isClockIn = true,
  }) : super(key: key);

  @override
  State<ClockInPage> createState() => _ClockInPageState();
}

class _ClockInPageState extends State<ClockInPage> {
  CameraController? _cameraController;
  bool _isLoading = false;
  String _statusMessage = "";

  @override
  void initState() {
    super.initState();
    _initializeCamera();
  }

  Future<void> _initializeCamera() async {
    if (cameras.isEmpty) return;
    CameraDescription frontCamera = cameras.firstWhere(
      (camera) => camera.lensDirection == CameraLensDirection.front,
      orElse: () => cameras.first,
    );
    _cameraController = CameraController(
      frontCamera,
      ResolutionPreset.medium,
      enableAudio: false,
    );
    await _cameraController!.initialize();
    if (mounted) setState(() {});
  }

  Future<void> _processAttendance() async {
    if (_cameraController == null || !_cameraController!.value.isInitialized) {
      return;
    }

    setState(() {
      _isLoading = true;
      _statusMessage = "Mengunci koordinat GPS...";
    });

    try {
      // Cek & minta izin lokasi
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      if (permission == LocationPermission.deniedForever) {
        _showDialog(
          "Izin Lokasi Ditolak",
          "Aktifkan izin lokasi di pengaturan HP untuk bisa absen.",
          isSuccess: false,
        );
        return;
      }

      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      setState(() => _statusMessage = "Mengambil foto selfie...");
      XFile photo = await _cameraController!.takePicture();
      File photoFile = File(photo.path);

      setState(() => _statusMessage = "Memverifikasi ke server...");

      Map<String, dynamic> result;
      if (widget.isClockIn) {
        result = await ApiService.clockIn(
          photoFile: photoFile,
          latitude: position.latitude,
          longitude: position.longitude,
        );
      } else {
        result = await ApiService.clockOut(
          photoFile: photoFile,
          latitude: position.latitude,
          longitude: position.longitude,
        );
      }

      if (result['status'] == 'success') {
        _showDialog(
          "Sukses 🎉",
          result['message'] ?? 'Absen berhasil',
          isSuccess: true,
        );
      } else {
        _showDialog(
          "Absen Ditolak ❌",
          result['message'] ?? 'Terjadi kesalahan',
          isSuccess: false,
        );
      }
    } catch (e) {
      _showDialog(
        "Sistem Error",
        "Terjadi kesalahan koneksi.\nDetail: ${e.toString()}",
        isSuccess: false,
      );
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
          _statusMessage = "";
        });
      }
    }
  }

  void _showDialog(String title, String content, {required bool isSuccess}) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        icon: Icon(
          isSuccess ? Icons.check_circle_rounded : Icons.cancel_rounded,
          color: isSuccess ? const Color(0xFF10B981) : Colors.redAccent,
          size: 48,
        ),
        title: Text(
          title,
          textAlign: TextAlign.center,
          style: const TextStyle(fontWeight: FontWeight.w800),
        ),
        content: Text(content, textAlign: TextAlign.center),
        actions: [
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: isSuccess
                    ? const Color(0xFF10B981)
                    : Colors.redAccent,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              onPressed: () {
                Navigator.pop(context);
                if (isSuccess) Navigator.pop(context);
              },
              child: const Text(
                "Tutup",
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _cameraController?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final Color themeColor = widget.isClockIn
        ? const Color(0xFF10B981)
        : const Color(0xFFEF4444);
    final String titleText = widget.isClockIn ? "Absen Masuk" : "Absen Pulang";

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Color(0xFF0F172A)),
        title: Text(
          titleText,
          style: const TextStyle(
            color: Color(0xFF0F172A),
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: _isLoading
          ? _buildLoadingState(themeColor)
          : _buildActiveState(themeColor),
    );
  }

  Widget _buildLoadingState(Color themeColor) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CircularProgressIndicator(color: themeColor, strokeWidth: 4),
          const SizedBox(height: 24),
          Text(
            _statusMessage,
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              color: Color(0xFF64748B),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActiveState(Color themeColor) {
    return Column(
      children: [
        const SizedBox(height: 20),

        // Info nama karyawan
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: const Color(0xFFF1F5F9)),
            ),
            child: Row(
              children: [
                Icon(Icons.person_rounded, color: themeColor, size: 20),
                const SizedBox(width: 8),
                Text(
                  widget.employeeData['name'] ?? '-',
                  style: const TextStyle(
                    fontWeight: FontWeight.w600,
                    fontSize: 14,
                    color: Color(0xFF0F172A),
                  ),
                ),
                const Spacer(),
                Text(
                  'NIP: ${widget.employeeData['nip'] ?? '-'}',
                  style: const TextStyle(
                    fontSize: 12,
                    color: Color(0xFF64748B),
                  ),
                ),
              ],
            ),
          ),
        ),

        const SizedBox(height: 16),
        const Text(
          "Posisikan wajah Anda di dalam bingkai",
          style: TextStyle(color: Color(0xFF64748B)),
        ),
        const SizedBox(height: 16),

        // Bingkai Kamera
        Center(
          child: Container(
            width: 300,
            height: 380,
            decoration: BoxDecoration(
              color: Colors.black12,
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: themeColor, width: 4),
            ),
            clipBehavior: Clip.antiAlias,
            child:
                (_cameraController != null &&
                    _cameraController!.value.isInitialized)
                ? CameraPreview(_cameraController!)
                : Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.camera_alt_rounded,
                          size: 48,
                          color: themeColor,
                        ),
                        const SizedBox(height: 8),
                        const Text("Menyiapkan Kamera..."),
                      ],
                    ),
                  ),
          ),
        ),

        const Spacer(),

        // Tombol Verifikasi
        Padding(
          padding: const EdgeInsets.fromLTRB(24, 0, 24, 32),
          child: SizedBox(
            width: double.infinity,
            height: 64,
            child: ElevatedButton.icon(
              onPressed: _isLoading ? null : _processAttendance,
              icon: const Icon(Icons.camera_alt_rounded, size: 28),
              label: Text(
                widget.isClockIn
                    ? "Absen Masuk Sekarang"
                    : "Absen Pulang Sekarang",
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                ),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: themeColor,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(20),
                ),
                elevation: 0,
              ),
            ),
          ),
        ),
      ],
    );
  }
}
