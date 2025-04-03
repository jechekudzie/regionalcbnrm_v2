import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:get/get.dart';

class ConnectivityService extends GetxService {
  final Connectivity _connectivity = Connectivity();
  final RxBool isConnected = true.obs;
  
  Future<ConnectivityService> init() async {
    // Get initial connection status
    ConnectivityResult result = await _connectivity.checkConnectivity();
    _updateConnectionStatus(result);
    
    // Listen for connection changes
    _connectivity.onConnectivityChanged.listen(_updateConnectionStatus);
    
    return this;
  }
  
  void _updateConnectionStatus(ConnectivityResult result) {
    isConnected.value = result != ConnectivityResult.none;
  }
}