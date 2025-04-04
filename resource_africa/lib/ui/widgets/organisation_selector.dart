import 'package:flutter/material.dart';
import 'package:resource_africa/models/user_model.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';

class OrganisationSelector extends StatefulWidget {
  final Organisation? selectedOrganisation;
  final Function(Organisation) onOrganisationChanged;

  const OrganisationSelector({
    super.key,
    this.selectedOrganisation,
    required this.onOrganisationChanged,
  });

  @override
  State<OrganisationSelector> createState() => _OrganisationSelectorState();
}

class _OrganisationSelectorState extends State<OrganisationSelector> {
  final OrganisationRepository _organisationRepository = OrganisationRepository();

  List<Organisation> _organisations = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadOrganisations();
  }

  Future<void> _loadOrganisations() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final organisations = await _organisationRepository.getUserOrganisations();
      setState(() {
        // The repository should now return unique organizations
        _organisations = organisations;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = 'Failed to load organisations';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      color: Colors.white,
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Organisation',
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: Colors.grey,
            ),
          ),
          const SizedBox(height: 8),
          _isLoading
              ? const SizedBox(
                  height: 48,
                  child: Center(
                    child: SizedBox(
                      width: 24,
                      height: 24,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                      ),
                    ),
                  ),
                )
              : _error != null
                  ? Row(
                      children: [
                        const Icon(Icons.error_outline, color: Colors.red),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            _error!,
                            style: const TextStyle(color: Colors.red),
                          ),
                        ),
                        TextButton(
                          onPressed: _loadOrganisations,
                          child: const Text('Retry'),
                        ),
                      ],
                    )
                  : _organisations.isEmpty
                      ? const Text('No organisations available')
                      : _buildDropdown(),
        ],
      ),
    );
  }

  Widget _buildDropdown() {
    return Container(
      height: 48,
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey.shade300),
        borderRadius: BorderRadius.circular(8),
      ),
      child: DropdownButtonHideUnderline(
        child: ButtonTheme(
          alignedDropdown: true,
          child: DropdownButton<Organisation>(
            isExpanded: true,
            value: widget.selectedOrganisation,
            hint: const Text('Select an organisation'),
            items: _organisations.map((org) {
              return DropdownMenuItem<Organisation>(
                value: org,
                child: Text(
                  org.name,
                  style: const TextStyle(fontSize: 16),
                  overflow: TextOverflow.ellipsis,
                ),
              );
            }).toList(),
            onChanged: (Organisation? newValue) {
              if (newValue != null) {
                widget.onOrganisationChanged(newValue);
              }
            },
          ),
        ),
      ),
    );
  }
}
