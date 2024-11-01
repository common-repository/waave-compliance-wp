=== WAAVE Compliance WP ===
Requires at least: 6.2
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.1.14
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WAAVE: Streamlining Compliance for Wellness Merchants.

== Description ==

WAAVE offers an unparalleled compliance solution tailored for merchants of high-risk wellness products, including kratom, CBD, hemp, vapes, and nutraceuticals. By navigating the intricacies of regulatory requirements across Federal, State, and City levels, WAAVE ensures businesses operate within the law, safeguarding your good status with your payment processor and acquiring bank.

== WaaveCompliance Features: ==

- **Inventory Management:** Automatically categorizes products, ensuring each complies with the specific rules of its industry. This keeps transactions compliant and straightforward.
- **Compliant Shipping:** Sets up specific shipping rules for each type of product and delivery, complying with legal requirements to prevent breaking the law.
Continuous Compliance Updates: WAAVE stays up-to-date with the latest laws and regulations for each product vertical and automatically adjusts the compliance stack accordingly.
- **COA Verification:** Ensures Certificates of Analysis are accurate and updated before payment processing is allowed. This guarantees all products comply with essential safety and regulatory standards for legitimate transactions.
- **COA Renewal and Management:** Never find yourself in trouble for selling items with expired Certificates of Analysis. Our system will alert you when COAs are expiring and allow you to request new certificates at a discounted price from the best DEA-approved labs in the country.
- **Dashboard Access:** A user-friendly dashboard is available for businesses to keep an eye on their products and payment activity, making it simpler to stay on top of compliance.
- **Built-in ID validation:** AI-driven tech ensures you check the ID for those products that require so by law at shipping destination. Seamless and non-intrusive, we validate once and verify each time as the law mandates.
- **Disclaimers and Claims monitoring:** WaaveCompliance will ensure the legal wording on your site is up to date, when the law changes the information is automatically updated, and you do not need to spend hours researching regulations changes at State or City level.

WAAVE is a proud partner of the American Kratom Association and The Hemp Round Table, we work together with advocates to protect the industries and the sellers who trust us with their compliance operations. WAAVE simplifies compliance, allowing wellness merchants to grow their businesses with confidence and regulatory ease.

== Dependency on Third-Party Service ==

WAAVE Compliance WP relies on a third-party service to retrieve and validate data. Here's how and under what circumstances the third-party service is involved:

1. **Data Retrieval:**
   - **API Calls:** The third-party service, [WAAVE](https://getwaave.co) makes HTTP requests to the plugin’s APIs to fetch product and category data. This is essential for ensuring that the third-party system can accurately sync and validate data against your WordPress site.
   - **Frequency:** These API calls can occur periodically or based on specific triggers set by the third-party service.

2. **Data Storage:**
   - **Third-Party Database:** The fetched data is stored in the third-party service’s database. This storage is used for validation purposes and might be retained as per their retention policies.

3. **Validation Processes:**
   - **Data Comparison:** The third-party service uses the stored data to compare and validate it against the live data from your WordPress site, ensuring consistency and accuracy.
   - **Notification of Discrepancies:** If discrepancies are found, the third-party service may notify you or take corrective actions as per their configuration.

4. **Disclaimers:**
   - **API Integration:** The plugin can call the third-party service’s API to retrieve disclaimers that are dynamically displayed at the bottom of your WordPress pages.
   - **Customization:** These disclaimers can be customized and managed through your controlled service.

== Important Considerations: ==

- **Data Privacy:** Ensure that you trust the third-party service with your data, as they will store and process it as described.
- **Security:** Verify that the third-party service uses secure methods for data transfer and storage.
- **Service Agreement:** Review any agreements or terms of service with the third-party to understand how your data will be handled. You can view the third-party service’s [Terms of Service](http://static.getwaave.co/info/tos.html).

For detailed information on how to configure and use the plugin, please refer to the [Installation](#installation) and [FAQ](#faq) sections below.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/[your-plugin-directory]` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the `/wp-admin/admin.php?page=wc-settings&tab=waave` to configure the plugin’s settings.

== Frequently Asked Questions ==

= How do I configure the APIs for third-party access? =
You can configure the API settings under the plugin’s settings page `/wp-admin/admin.php?page=wc-settings&tab=waave` in the WordPress admin.

= What security measures are in place for the APIs? =
The plugin uses authentication methods like API keys or tokens to ensure secure access. Additionally, we recommend using HTTPS to protect data in transit.

= How does the plugin block payments? =
During the checkout process, the plugin checks if the order complies with the defined shipping rules. If any rule is violated, the plugin will block the payment and display an appropriate message to the customer.

== Screenshots ==
1. Compliance Dashboard
2. Compliance Inventory Monitoring
3. Compliance Updates
4. COAs Management
5. COAs Management

== Changelog ==

= [Version 1.0.0] =
- Initial release of WAAVE Compliance WP plugin.

== Upgrade Notice ==

= [Version 1.1.14] =
- Added improved customization options for the age confirmation popup.

== Support and Contact Information ==

* Web: www.getwaave.com
* Social media:
  - Linkedin: www.LinkedIn.com/company/getwaave
  - Facebook: www.facebook.com/getwaavenow
  - Instagram: www.instagram.com/getwaave
* Email: sales@getwaave.com
* Phone Number: (888) 439-0240
* Trustpilot: https://www.trustpilot.com/review/getwaave.com

== License ==

This plugin is licensed under the GPLv2 or later. See the [GNU General Public License](http://www.gnu.org/licenses/gpl-2.0.html) for more details.

