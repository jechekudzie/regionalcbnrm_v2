import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';

class SimpleLocationPicker extends StatefulWidget {
  final double initialLatitude;
  final double initialLongitude;
  final Function(double, double) onLocationChanged;

  const SimpleLocationPicker({
    super.key,
    required this.initialLatitude,
    required this.initialLongitude,
    required this.onLocationChanged,
  });

  @override
  State<SimpleLocationPicker> createState() => _SimpleLocationPickerState();
}

class _SimpleLocationPickerState extends State<SimpleLocationPicker> {
  late MapController _mapController;
  late LatLng _currentPosition;
  
  @override
  void initState() {
    super.initState();
    _mapController = MapController();
    _currentPosition = LatLng(
      widget.initialLatitude != 0 ? widget.initialLatitude : -18.3825,
      widget.initialLongitude != 0 ? widget.initialLongitude : 30.0000,
    );
  }
  
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        SizedBox(
          height: 250,
          child: ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: FlutterMap(
              mapController: _mapController,
              options: MapOptions(
                center: _currentPosition,
                zoom: 13,
                maxZoom: 18,
                minZoom: 4,
                onTap: (tapPosition, point) {
                  setState(() {
                    _currentPosition = point;
                  });
                  widget.onLocationChanged(point.latitude, point.longitude);
                },
              ),
              children: [
                TileLayer(
                  urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                  userAgentPackageName: 'com.resource_africa.app',
                ),
                MarkerLayer(
                  markers: [
                    Marker(
                      point: _currentPosition,
                      width: 40,
                      height: 40,
                      builder: (context) => const Icon(
                        Icons.location_on,
                        color: Colors.red,
                        size: 40,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 8),
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'Lat: ${_currentPosition.latitude.toStringAsFixed(6)}, Lng: ${_currentPosition.longitude.toStringAsFixed(6)}',
              style: const TextStyle(fontSize: 12, color: Colors.grey),
            ),
            TextButton.icon(
              onPressed: () {
                // Set to a default location for now
                const defaultPosition = LatLng(-18.3825, 30.0000);
                setState(() {
                  _currentPosition = defaultPosition;
                });
                _mapController.move(defaultPosition, 15);
                widget.onLocationChanged(defaultPosition.latitude, defaultPosition.longitude);
              },
              icon: const Icon(Icons.location_searching),
              label: const Text('Default Location'),
            ),
          ],
        ),
      ],
    );
  }
}