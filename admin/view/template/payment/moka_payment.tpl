<?php echo $header; ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" >

    <div id="content">
        <style>
            .box > .content{

                overflow: none!important;
            }
            .zbtn {
                border: none;
                font-family: inherit;
                font-size: 13px;
                color: white !important;
                background: none;
                cursor: pointer;
                padding: 25px 80px;
                display: inline-block;

                margin: 15px 30px;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 700;
                max-width: 350px;
                min-width: 350px;
                outline: none;
                position: relative;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                transition: all 0.3s;
            }
            .btn-2a {
                border-radius: 0 0 5px 5px;
            }

            .btn-2a:hover {
                box-shadow: 0 4px #e91027;
                top: 2px;
            }

            .btn-2a:active {
                box-shadow: 0 0 #e91027;
                top: 6px;
            }
            .btn-2 {
                background: #ff2b42;
                color: #fff;
                box-shadow: 0 6px #e91027;
                -webkit-transition: none;
                -moz-transition: none;
                transition: none;
            }

        </style>

        <div class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            <?php } ?>
        </div>
        <?php if ($message) { ?>
            <div class="<?php echo $hasError ? 'warning' : 'success'; ?>"><?php echo $message; ?></div>
        <?php } ?>
        <?php if ($error_warning) { ?>
            <div class="warning"><?php echo $error_warning; ?></div>
        <?php } ?>
        <?php if ($error_version) { ?>
            <div class="warning"><?php echo $error_version; ?> <a href="<?php echo $version_update_link; ?>"> <?php echo $moka_update_button; ?></a> <?php echo $moka_or_text; ?></div>
        <?php } ?>
        <div class="box">
            <div class="heading">
                <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
                <div class="buttons"><a onclick="$('#form').submit();" class="button" id="saveKeys"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
            </div>
            <div class="content">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                    <table class="form">

                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_dealercode; ?></td>
                            <td><input type="text" name="moka_payment_dealercode" value="<?php echo $moka_payment_dealercode; ?>" />
                        <?php if ($error_dealercode) { ?>
                            <span class="error"><?php echo $error_dealercode; ?></span>
                        <?php } ?>
                        </td>
                        </tr>
                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_username; ?></td>
                            <td><input type="text" name="moka_payment_username" value="<?php echo $moka_payment_username; ?>" />
                        <?php if ($error_username) { ?>
                            <span class="error"><?php echo $error_username; ?></span>
                        <?php } ?>
                        </td>
                        </tr>
                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_password; ?></td>
                            <td><input type="text" name="moka_payment_password" value="<?php echo $moka_payment_password; ?>" />
                        <?php if ($error_password) { ?>
                            <span class="error"><?php echo $error_password; ?></span>
                        <?php } ?>
                        </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_status; ?></td>
                            <td><select name="moka_payment_status">
                                    <?php if ($moka_payment_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                    <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_threed; ?></td>
                            <td><select name="moka_payment_moka_3d_mode">
                                    <?php if ($moka_payment_moka_3d_mode == "OFF") { ?>
                                        <option value="ON"><?php echo $text_enabled; ?></option>
                                        <option value="OFF" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php } else { ?>
                                        <option value="ON" selected="selected"><?php echo $text_enabled ?></option>
                                        <option value="OFF"><?php echo $text_disabled; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><?php echo $entry_installement; ?></td>
                            <td><select name="moka_payment_installement">
                                    <?php if ($moka_payment_installement == "OFF") { ?>
                                        <option value="ON"><?php echo $text_enabled; ?></option>
                                        <option value="OFF" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php } else { ?>
                                        <option value="ON" selected="selected"><?php echo $text_enabled ?></option>
                                        <option value="OFF"><?php echo $text_disabled; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><?php echo $entry_order_status; ?></td>
                            <td><select name="moka_payment_order_status_id">
                                    <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $moka_payment_order_status_id) { ?>
                                            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_cancel_order_status; ?></td>
                            <td><select name="moka_payment_cancel_order_status_id">
                                    <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $moka_payment_cancel_order_status_id) { ?>
                                            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $entry_sort_order; ?></td>
                            <td><input type="text" name="moka_payment_sort_order" value="<?php echo $moka_payment_sort_order; ?>" size="1" /></td>
                        </tr>
                    </table>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
                        </div>




                        <div class="tab-pane" id="tab-moka_rates">
                            <div class="form-group">
                                <?php echo $moka_rates_table ?>
                            </div>
                            <input type="hidden" name="moka_rates_submit" value="1"/>
                            <input type="hidden" name="moka_registered" value="ok"/>
                            <button type="submit"  data-toggle="tooltip" title="Oranları kaydet" class="btn btn-primary"><?php echo $button_save; ?></button>

                            </form>
                        </div>
                        <div class="tab-pane" id="tab-moka_about">
                            <div class="panel">
                                <div class="row kahvedigital_moka-header">
                                    <img src="../catalog/view/theme/default/image/moka_payment/logo.png" class="col-xs-4 col-md-2 text-center" id="payment-logo" />
                                    <div class="col-xs-6 col-md-5 text-center">
                                        <h4>Moka Ödeme Kuruluşu A.Ş.</h4>
                                        <h4>Hızlı Güvenli ve Kolay</h4>
                                    </div>
                                    <div class="col-xs-12 col-md-5 text-center">
                                        <a href="https://moka.com" class="btn btn-primary" id="create-account-btn">Moka SanalPOS'a başvurun</a><br />
                                        Moka SanalPOS'unuz varsa ?<a href="https://pos.moka.com"> Hesabınıza giriş yapın</a>
                                    </div>
                                </div>

                                <hr />


                                <div class="kahvedigital_moka-content">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="thumbnail">
                                                <figure class="figure text-center">
                                                    <img src="../catalog/view/theme/default/image/moka_payment/icons/icon_clock.png" width="140" height="100"/>
                                                </figure>
                                                <p class="text text-center">
                                                    7x24 kesintisiz
                                                    <br>tahsilat imkanı
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="thumbnail">
                                                <figure class="figure text-center">
                                                    <img src="../catalog/view/theme/default/image/moka_payment/icons/icon_money.png" width="140" height="100"/>
                                                </figure>
                                                <p class="text text-center">
                                                    Hesaplı
                                                    <br>satış avantajı
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="thumbnail">
                                                <figure class="figure text-center">
                                                    <img src="../catalog/view/theme/default/image/moka_payment/icons/icon_credit_card.png" width="140" height="100"/>
                                                </figure>
                                                <p class="text text-center">
                                                    Bütün kredi kartları için
                                                    <br>taksitli satış imkanı
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="thumbnail">
                                                <figure class="figure text-center">
                                                    <img src="../catalog/view/theme/default/image/moka_payment/icons/icon_visa_mastercard.png" width="140" height="100"/>
                                                </figure>
                                                <p class="text text-center">
                                                    Visa ve MasterCard
                                                    <br>tahsilat imkanı
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="thumbnail">
                                                <figure class="figure text-center">
                                                    <img src="../catalog/view/theme/default/image/moka_payment/icons/icon_exchange.png" width="140" height="100"/>
                                                </figure>
                                                <p class="text text-center">
                                                    Yabancı kartlar ile
                                                    <br>işlem yapabilme
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="thumbnail">
                                                <figure class="figure text-center">
                                                    <img src="../catalog/view/theme/default/image/moka_payment/icons/icon_cogs.png" width="140" height="100"/>
                                                </figure>
                                                <p class="text text-center">
                                                    Hızlı ve kolay
                                                    <br>entegrasyon
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr />
                                </div>
                            </div>					





                        </div>
                        <div class="tab-pane" id="tab-moka_help">

                            <div class="panel">
                                <div class="row">   
                                    <div class="alert alert-success"> Bu plugin için teknik destek işlemleri Moka Ödeme A.Ş. adına <a href="https://kahvedigital.com">KahveDigital</a> tarafından <b>ÜCRETSİZ</b> sağlanmaktadır.</div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 text-center">            

                                        <div class="row">
                                            <div class="col-sm-2"></div>
                                            <img src="../catalog/view/theme/default/image/moka_payment/kahvedigital-help.jpg" class="col-sm-8 text-center" id="payment-logo" />
                                            <div class="col-sm-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 panel text-center">
                                        <h1>Destek</h1><hr>
                                        <a class="zbtn btn-2 btn-2a" href="http://docs.kahvedigital.com/moka/prestashop"> Kullanım Klavuzu</a><br>
                                        <a class="zbtn btn-2 btn-2a" href="http://client.kahvedigital.com/admin/login/signin">Destek Sistemi</a><br>
                                        <a class="zbtn btn-2 btn-2a">+90(0212)570 81 29</a><br>
                                        <a class="zbtn btn-2 btn-2a" href="mailto:destek@kahvedigital.com">destek@kahvedigital.com</a>

                                    </div>

                                    <hr/>
                                </div>

                                <div class="panel">

                                    <div class="col-sm-12 text-center">
                                        <a href="https://www.facebook.com/kahvedigital/"><img src="../catalog/view/theme/default/image/moka_payment/icons/facebook.png" width="32px" /></a>
                                        <a href="https://twitter.com/kahvedigital"><img src="../catalog/view/theme/default/image/moka_payment/icons/twitter.png" width="32px" /></a>
                                        <a href="https://www.youtube.com/user/kahvedigital"><img src="../catalog/view/theme/default/image/moka_payment/icons/youtube.png" width="32px" /></a>
                                        <a href="https://www.linkedin.com/company/kahve-digital/"><img src="../catalog/view/theme/default/image/moka_payment/icons/linkedin.png" width="32px" /></a>
                                        <a href="https://www.instagram.com/kahvedigital/"><img src="../catalog/view/theme/default/image/moka_payment/icons/instagram.png" width="32px" /></a>
                                        <a href="https://wordpress.org/support/users/kahvedigital"><img src="../catalog/view/theme/default/image/moka_payment/icons/wordpress.png" width="32px" /></a>
                                        <a href="https://github.com/kahvedigital/"><img src="../catalog/view/theme/default/image/moka_payment/icons/github.png" width="32px" /></a>
                                    </div>
                                    <hr/>
                                </div>
                            </div>
                        </div>



                </form>
            </div>
        </div>
    </div>
    <?php echo $footer; ?>


    <style type="text/css">

        #form input[type='text'], input[type='password'] {
            width: 250px;
        }

    </style>
    <script type="text/javascript">
        window.onload = function () {

            var response = '<?php echo $response ?>';
            if (response == 1) {
                var el = document.getElementById('saveKeys');
                el.click();
            }
        };
    </script>