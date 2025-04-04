import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';

class LocationMapView extends StatelessWidget {
  final double latitude;
  final double longitude;
  final double zoom;
  final bool interactive;
  final bool readOnly;
  final String? title;

  const LocationMapView({
    super.key,
    required this.latitude,
    required this.longitude,
    this.zoom = 13,
    this.interactive = true,
    this.readOnly = false,
    this.title,
  });

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(12),
      child: FlutterMap(
        options: MapOptions(
          center: LatLng(latitude, longitude),
          zoom: zoom,
          maxZoom: 18,
          minZoom: 4,
          interactiveFlags: interactive
              ? InteractiveFlag.all
              : InteractiveFlag.none,
        ),
        children: [
          TileLayer(
            urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            userAgentPackageName: 'com.resource_africa.app',
          ),
          MarkerLayer(
            markers: [
              Marker(
                point: LatLng(latitude, longitude),
                width: 40,
                height: 40,
                child: const Icon(
                  Icons.location_on,
                  color: Colors.red,
                  size: 40,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
