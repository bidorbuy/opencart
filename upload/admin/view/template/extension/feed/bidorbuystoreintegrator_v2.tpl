<?php

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-google-analytics" data-toggle="tooltip"
                        title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <?php if ($warning) { ?>
        <?php foreach ($warning as $warn) : ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $warn; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endforeach; ?>
        <?php } ?>

        <?php if ($success) { ?>
        <?php foreach ($success as $item) : ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $item; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endforeach; ?>
        <?php } ?>


        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
                <div class="buttons">
                    <a id="submit" onclick="
                    jQuery('#bobsi-exc-categories option').prop('selected', 'selected');
                    jQuery('#bobsi-inc-statuses option').prop('selected', 'selected');
                    $('#form').submit();
                " class="button"><?php echo $button_save; ?></a>
                    <a id="toolbar-export" href="" class="button"><?php echo $button_export; ?></a>
                    <a id="toolbar-download" href="" class="button"><?php echo $button_download; ?></a>
                    <a id="toolbar-reset" onclick="$('#bobsi-export-links').submit();"
                       class="button"><?php echo $button_reset_tokens; ?></a>
                </div>

            </div>
            <div class="panel-body">
                <div class="bob-admin-head">
                    <div id="logo">
                        <img src="view/image/bidorbuystoreintegrator/bidorbuy.png">
                    </div>

                    <div id="bobsi-adv">
                        <!-- BEGIN ADVERTPRO CODE BLOCK -->
                        <script type="text/javascript">
                            document.write('<scr' + 'ipt src="http://nope.bidorbuy.co.za/servlet/view/banner/javascript/zone?zid=153&pid=0&random=' + Math.floor(89999999 * Math.random() + 10000000) + '&millis=' + new Date().getTime() + '&referrer=' + encodeURIComponent(document.location) + '" type="text/javascript"></scr' + 'ipt>');
                        </script>
                        <!-- END ADVERTPRO CODE BLOCK -->
                    </div>
                </div>

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                    <div class="postbox postbox-left">
                        <table id="config" class="form-table">
                            <thead class="bobsi-title">
                            <tr>
                                <td colspan="2" class="left"><?php echo $export_configs_title; ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Feature #3751 -->
                            <!-- File Name -->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[2]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[2]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[2]->desc; ?></span>
                                </td>
                            </tr>
                            <!-- Compress -->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[3]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[3]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[3]->desc; ?></span>
                                </td>
                            </tr>
                            <!-- Min quantity  -->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[4]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[4]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[4]->desc; ?></span>
                                </td>
                            </tr>
                            </tbody>

                            <tfoot>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="postbox postbox-right">
                        <table id="criteria" class="form-table">
                            <thead>
                            <tr>
                                <td colspan="3" class="left"><?php echo $export_criteria_title; ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($exportCriteriaFieldSet as $field) : ?>
                            <tr>
                                <?php if (isset($field->label)) { ?>
                                <th>
                                    <?php echo $field->label; ?>
                                </th>
                                <td>
                                    <?php  echo $field->input; ?>
                                    <span class="help"><?php  echo $field->desc; ?></span>
                                </td>
                                <?php } else { ?>

                                <td colspan="2">
                                    <fieldset>
                                        <?php  echo $field->input; ?>
                                    </fieldset>
                                </td>

                                <?php } ?>

                            </tr>
                            <?php endforeach; ?>
                            </tbody>

                            <tfoot>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="<?php echo bobsi\Settings::nameTokenDownload; ?>"
                               value="<?php echo $token_download; ?>">
                        <input type="hidden" name="<?php echo bobsi\Settings::nameTokenExport; ?>"
                               value="<?php echo $token_export; ?>">

                    </div>
                    <!--  FEATURE 3751 ------------------------------------------------------------------------------------>
                    <div class="postbox debug postbox-inner">
                        <table id="config" class="form-table">
                            <thead class="bobsi-title">
                            <tr>
                                <td colspan="2" class="left">Debug</td>
                            </tr>
                            </thead>
                            <tbody>

                            <!-- Feature 3910 - BAA block -->
                            <?php if($baa) :?>

                            <tr>
                                <td colspan="2" style="font-size: 14px">
                                    <span><b>Basic Access Authentication</b></span><br>
                                    <span>(if necessary)</span><br><br>
                                     <span style="color: red">
                                             Do not enter username or password of ecommerce platform,
                                             please read carefully about this kind of authentication!
                                        </span>
                                </td>
                            </tr>
                            <!-- Feature #3751 -->
                            <!-- UserName-->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[0]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[0]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[0]->desc; ?></span>
                                </td>
                            </tr>
                            <!-- Password -->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[1]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[1]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[1]->desc; ?></span>
                                </td>
                            </tr>

                            <?php else: ?>

                            <input type="hidden" name="<?= bobsi\Settings::nameUsername ?>" value ="<?= $formdata[bobsi\Settings::nameUsername]?>" />
                            <input type="hidden" name="<?= bobsi\Settings::namePassword ?>" value ="<?= $formdata[bobsi\Settings::namePassword]?>" />

                            <?php endif; ?>

                            <tr>
                                <td><b style="font-size: 14px">Logs</b></td>
                            </tr>
                            <!-- Email -->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[5]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[5]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[5]->desc; ?></span>
                                </td>
                            </tr>
                            <!-- Turn on -->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[6]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[6]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[6]->desc; ?></span>
                                </td>
                            </tr>
                            <!-- Logging Level -->
                            <tr>
                                <th>
                                    <?php echo $exporConfigstFieldSet[7]->label; ?>
                                </th>
                                <td class="input">
                                    <?php  echo $exporConfigstFieldSet[7]->input; ?>
                                    <span class="help"><?php  echo $exporConfigstFieldSet[7]->desc; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="buttons" align="right">
                                        <a id="submit" onclick="
                                    jQuery('#bobsi-exc-categories option').prop('selected', 'selected');
                                    jQuery('#bobsi-inc-statuses option').prop('selected', 'selected');
                                    $('#form').submit();" class="button"><?php echo $button_save; ?></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                </form>
                <!-- log -->
                <?php echo $logsHtml; ?>
            </div>

            <form action="<?php echo $reset; ?>" method="post"
                  id="bobsi-export-links"
                  name="bobsi-export-form"
                  enctype="multipart/form-data">

                <div class="postbox links postbox-inner">
                    <input class="bobsi-input" type="hidden"
                           id="<?php echo bobsi\Settings::nameActionReset; ?>"
                           name="<?php echo bobsi\Settings::nameActionReset; ?>"
                           value="1"/>

                    <table id="module" class="form-table export-links">
                        <thead>
                        <tr>
                            <td colspan="3" class="left">Links</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="narrow"><label for="tokenExportUrl"><?php echo($export_url); ?></label></td>
                            <td>
                                <input type="text" id="tokenExportUrl" class="bobsi-url"
                                       title="<?php echo $link_tooltip; ?>"
                                       value="<?php echo $export_link; ?>" readonly/>
                            </td>
                            <td class="narrow button-section right">
                                <a class="button"
                                   onclick="window.open('<?php echo $export_link; ?>','_blank');"><?php echo $launch; ?></a>
                                <a class="button copy-button"><?php echo $copy; ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="tokenDownloadUrl"><?php echo($download_url); ?></label></td>
                            <td>
                                <input type="text" id="tokenDownloadUrl" class="bobsi-url"
                                       title="<?php echo $link_tooltip; ?>"
                                       value="<?php echo $download_link; ?>" readonly/>
                            </td>
                            <td class="button-section right">
                                <a class="button button-primary"
                                   onclick="window.open('<?php echo $download_link; ?>','_blank');"><?php echo $launch; ?></a>
                                <a class="button copy-button"><?php echo $copy; ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="right"><a onclick="$('#bobsi-export-links').submit();"
                                                             class="button"><?php echo $reset_tokens; ?></a></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </form>

            <div class="postbox version postbox-inner">
                <table id="version" class="form-table">
                    <thead>
                    <tr><td colspan="3" class="left">Version</td></tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <a href="<?php echo $phpInfo_link; ?>" target="_blank">@See PHP information</a>
                        </td>
                    </tr>
                    <tr><td><?php echo bobsi\Version::getLivePluginVersion(); ?></td></tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>