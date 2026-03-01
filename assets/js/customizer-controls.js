/**
 * Gizmodotech Pro — Customizer Controls
 * Handles conditional display of fields based on API selection
 */

(function($) {
    'use strict';

    wp.customize.bind('ready', function() {
        
        // Function to toggle fields based on API type
        function toggleApiFields(apiType) {
            var isCreators = (apiType === 'creators');
            var isPaapi5 = (apiType === 'paapi5');
            
            // Get the control elements
            var $clientIdControl = $('#customize-control-gizmo_creators_client_id');
            var $clientSecretControl = $('#customize-control-gizmo_creators_client_secret');
            var $versionControl = $('#customize-control-gizmo_amazon_credential_version');
            
            // Update labels based on API type
            if (isCreators) {
                $clientIdControl.find('.customize-control-title').text('Credential ID (Creators API)');
                $clientIdControl.find('.description').html('From Associates Central > Tools > Creators API<br>Example: amzn1.application-oa2-client.xxx');
                
                $clientSecretControl.find('.customize-control-title').text('Credential Secret (Creators API)');
                $clientSecretControl.find('.description').html('Shown once when creating credentials. Keep secure!');
                
                $versionControl.show();
                $versionControl.find('.customize-control-title').text('API Version');
                $versionControl.find('.description').text('Enter version shown in credentials (e.g., 3.2, 2.2)');
                
            } else if (isPaapi5) {
                $clientIdControl.find('.customize-control-title').text('Access Key ID (PA API 5.0)');
                $clientIdControl.find('.description').html('From Product Advertising API > Manage Your Credentials<br>Example: AKIA... or AKPA...');
                
                $clientSecretControl.find('.customize-control-title').text('Secret Access Key (PA API 5.0)');
                $clientSecretControl.find('.description').html('Keep this secure! Never share it.');
                
                $versionControl.hide();
            }
        }
        
        // Listen for API type changes
        wp.customize('gizmo_amazon_api_type', function(value) {
            value.bind(function(newVal) {
                toggleApiFields(newVal);
            });
        });
        
        // Initial toggle on page load
        var initialApiType = wp.customize('gizmo_amazon_api_type').get();
        toggleApiFields(initialApiType);
        
    });

})(jQuery);
