# OpenCart bidorbuy Store Integrator

### Compatibility

| Product | PHP version  | Platform |
| ------- | --- | --- |
| Store Integrator-2.0.11 | 5.4 | ✓ OpenCart 1.5.6.2, 2.1.0, 2.2.0, 2.3.0.2 |
| Store Integrator-2.0.10 | 5.4 | ✓ OpenCart 1.5.6.2, 2.1.0, 2.2.0, 2.3.0.2 |
| Store Integrator-2.0.9 | 5.3 | ✓ OpenCart 1.5.6.2, 2.1.0, 2.2.0, 2.3.0.2 |
| Store Integrator-2.0.8 | 5.3 | ✓ OpenCart 1.5.6.2, 2.1.0, 2.2.0, 2.3.0.2 |

### Description 

The bidorbuy Store Integrator allows you to get products from your online store listed on bidorbuy quickly and easily.
Expose your products to the bidorbuy audience - one of the largest audiences of online shoppers in South Africa Store updates will be fed through to bidorbuy automatically, within 24 hours so you can be sure that your store is in sync within your bidorbuy listings. All products will appear as Buy Now listings. There is no listing fee just a small commission on successful sales. View [fees](https://support.bidorbuy.co.za/index.php?/Knowledgebase/Article/View/22/0/fee-rate-card---what-we-charge). Select as many product categories to list on bidorbuy as you like. No technical requirements necessary.

To make use of this plugin, you'll need to be an advanced seller on bidorbuy.
 * [Register on bidorbuy](https://www.bidorbuy.co.za/jsp/registration/UserRegistration.jsp?action=Modify)
 * [Apply to become an advanced seller](https://www.bidorbuy.co.za/jsp/seller/registration/UserSellersRequest.jsp)
 * Once you integrate with bidorbuy, you will be contacted by a bidorbuy representative to guide you through the process.

### System requirements
- Supported PHP version: 5.4
- PHP extensions: curl, mbstring

### Installation OpenCart 1.5, OpenCart 2.1, OpenCart 2.2:

1. Unzip all the files in a local directory from 'storeintegrator-opencart-x.x.x-1.5.x-2.2.x.ocmod' archive.
2. (Optional) In case you have changed the admin`s folder name of your OpenCart installation - please rename /admin folder of the extension accordingly.
3. Upload files in the public_html folder of your site. You have to upload the plugin folder in the same place. 
4. Log in to control panel as administrator.
5. OpenCart 1.5.x: Go to Extensions > Product Feeds > bidorbuy Store Integrator > press Install button.
6. OpenCart 2.2.x: Go to Extensions > Feed > Feed List > bidorbuy Store Integrator > press Install button.

### Installation OpenCart 2.3

1. Unzip all the files in a local directory from 'storeintegrator-opencart-x.x.x-2.3.x-latest.ocmod' archive.
2. (Optional) In case you have changed the admin`s folder name of your OpenCart installation - please rename /admin folder of the extension accordingly.
3. Upload files in the public_html folder of your site. You have to upload the plugin folder in the same place. 
4. Log in to control panel as administrator.
5. OpenCart 2.3: Go to Extensions > Feeds (4)> bidorbuy Store Integrator > press Install button.

### Uninstallation

1. Log in to control panel as administrator.
2. OpenCart 1.5: Go to Extensions > Product Feeds > bidorbuy Store Integrator.
3. OpenCart 2.2: Go to Extensions > Feed > Feed List > bidorbuy Store Integrator.
4. OpenCart 2.3: Go to Extensions > Feeds (4)> bidorbuy Store Integrator.
5. Uninstall the bidorbuy Store Integrator.
6. Using your FTP program delete all the bidorbuy Store Integrator's files from the filesystem.

### Upgrade

Remove all old files of previous installation and re-install the archive (please look through the installation chapter). Where to find the files via FTP:
1. Root folder > catalog folder > controller > feed > bidorbuystoreintegrator.php;
2. Root folder > system folder > bidorbuystoreintegrator;
3. Root folder > bidorbuystoreintegrator folder;
4. YOUR_ADMIN folder > controller > feed > bidorbuystoreintegrator.php;
5. YOUR_ADMIN folder > language > english > feed > bidorbuystoreintegrator.php;
6. YOUR_ADMIN folder > model > bidorbuystoreintegrator;
7. YOUR_ADMIN folder > view > image > bidorbuystoreintegrator;
8. YOUR_ADMIN folder > view > javascript > bidorbuystoreintegrator;
9. YOUR_ADMIN folder > view > stylessheet > bidorbuystoreintegrator;
10. YOUR_ADMIN folder >view > template > feed > bidorbuystoreintegrator.tpl;
11. YOUR_ADMIN folder > view > template > feed > bidorbuystoreintegrator_v2.x.tpl.

Notice: If you had wrongly uploaded the Store Integrator version (after that you can not access to Open Cart extensions page, it says 'not found'): please delete the '*feed*' folder and re-install the proper version of archive (please look through the installation chapter):

* For OpenCart 1.5-2.2: delete *feed* folder at /admin/controller/extension;
* For OpenCart 2.3: delete *feed* folder at /admin/controller/*feed* folder.

### Configuration

1. Log into control panel as administrator.
2. OpenCart 1.5: Go to Extensions > Product Feeds > bidorbuy Store Integrator > Edit.
3. OpenCart 2.2: Go to Extensions > Feed > Feed List > bidorbuy Store Integrator > Edit.
4. OpenCart 2.3: Go to Extensions > Feeds (4) > bidorbuy Store Integrator > Edit.
5. Set the export criteria.
6. Press the `Save` button.
7. Press the `Export` button.
8. Press the `Download` button.
9. Share Export Links with bidorbuy.
10. To display BAA fields on the setting page add '&baa=1' to URL in address bar.