<?php
// SSO for Azure AD - Login form
// Adds the "Login with Azure AD" button to the login form.

// Die if not called from WordPress
if ( ! defined( 'WPINC' ) ) {
    die;
}

function sso_for_azure_ad_login_form() {
    // Checking if the "Hide Login button" option is enabled
    if ( get_option( 'sso_for_azure_ad_login_button_hide', false ) ) {
  	    // "Hide Login button" is activated, don't return anything
	    return;
    }

    $client_id = get_option( 'sso_for_azure_ad_client_id', '' );
    $client_secret = get_option( 'sso_for_azure_ad_client_secret', '' ); // only to check if it is set
    $tenant_id = get_option( 'sso_for_azure_ad_tenant_id', '' );

    $button_text = get_option( 'sso_for_azure_ad_login_button_text', __( 'Sign in with Azure AD', 'sso-for-azure-ad' ) );

    if (
	    $client_id == ''
	    || $client_secret == ''
	    || $tenant_id == ''
    ) {
  	    // Plugin is not fully configured, don't do anything.
	    return;
    }

    $login_url = sso_for_azure_ad_endpoint_url( 'start' );
    // Checking $_REQUEST because redirect_to isn't available in WP_Query and it may be set either in GET or POST.
    if ( isset( $_REQUEST['redirect_to'] ) ) {
        $login_url = add_query_arg( 'redirect_to', $_REQUEST['redirect_to'], $login_url );
    }

    ?>
        <a id="sso_for_azure_ad_start" href="<?php print( esc_html( $login_url ) ); ?>" style="width: 100%; text-align: center;" class="button button-primary button-large" width="100%"><?php print( esc_html( $button_text ) ); ?></a>
        <div style="clear: both; padding-top: 5px;"></div>
    <?php

    // Only return POST-redirecting code if POST redirects are required to ensure consistency between JS-enabled and JS-disabled when the option is disabled
    if ( get_option( 'sso_for_azure_ad_require_post_start', true ) ) {
        // The script uses var to avoid breaking compatibility with old browsers.
        ?>
            <script>
                document.getElementById('sso_for_azure_ad_start').addEventListener('click', function(e) {
                    var form = document.createElement('form');
                    form.action = e.target.href;
                    form.method = 'POST';
                    form.style.display = 'none';
                    document.body.appendChild(form);
                    form.submit();
                    e.preventDefault();
                });
            </script>
        <?php
    }
}

add_action( 'login_form', 'sso_for_azure_ad_login_form' );
