<div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="font-size:22px" id="global-settings" onclick="toggleSection(this, 'global')">Global Settings <i class="fa-solid fa-chevron-up fa-2xs" style="margin-left:10px"></i></h3>
            <!-- Global Settings -->
            <div style="padding-top: 20px" id="global" hidden>
                <?php
                // if ((isset($_GET['section']) && $_GET['section'] == 'global-settings')) {
                //     showResponse();
                // }
                ?>
                {!! $response_handling !!}
                <form id="globalForm" enctype="multipart/form-data" action="{{ route('admin.globalSettings') }}" method="POST">
                    @csrf
                    <table id="globalTable">
                        <tbody>
                            <tr class="" id="ldap-headings">
                                <th style="width:250px;margin-left:25px;padding-bottom:20px"></th>
                                <th style="width: 250px;padding-bottom:20px">Change</th>
                                <th style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">Current</th>
                                <th style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">Default</th>
                            </tr>
                            <tr class="">
                                <td id="system_name-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="system_name">System Name:</p>
                                </td>
                                <td id="system_name-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control theme-input-alt" type="text" style="width: 150px" id="system_name" name="system_name">
                                </td>
                                <td style="min-width:230px;margin-left:10px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['system_name'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['system_name'] }}</span></label>
                                </td>
                            </tr>
                            <tr class="" id="banner-color" style="margin-top:20px">
                                <td id="banner-color-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <!-- Custodian Colour: #72BE2A -->
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="banner_color">Banner Colour:</p>
                                </td>
                                <td id="banner-color-picker" style="width:250px;padding-bottom:20px">
                                    <label class="tag-color">
                                        <input class="form-control input-color color theme-input-alt" id="banner_color" name="banner_color" placeholder="#XXXXXX" data-value="#xxxxxx" value="{{ $head_data['config']['banner_color'] }}"/>
                                    </label>
                                </td>
                                <td style="min-width:230px;padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni" style="color: {{ $head_data['extras']['banner_text_color'] }} ;background-color: {{ $head_data['config']['banner_color'] }}">{{ $head_data['config']['banner_color'] }}</span></label>
                                </td>
                                <td style="min-width:230px;padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni" style="color: {{ $head_data['extras']['default_banner_text_color'] }} ;background-color:{{ $head_data['default_config']['banner_color'] }}">{{ $head_data['default_config']['banner_color'] }}</span></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px" id="banner-logo">
                                <td id="banner-logo-label" style="width:250px;margin-left:25px;padding-bottom:20px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="logo_image">Banner Logo:</p>
                                </td>
                                <td id="banner-logo-file">
                                    <input class="" type="file" style="width: 250px;padding-bottom:20px" id="logo_image" name="logo_image">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['config']['logo_image'] }}" style="width:50px" onclick="modalLoad(this)" /></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['default_config']['logo_image'] }}" style="width:50px" onclick="modalLoad(this)" /></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px" id="favicon-image">
                                <td id="favicon-image-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="favicon_image">Favicon Image:</p>
                                </td>
                                <td id="favicon-image-file" style="padding-bottom:20px">
                                    <input class="" type="file" style="width: 250px" id="favicon_image" name="favicon_image">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['config']['favicon_image'] }}" style="width:32px" onclick="modalLoad(this)" /></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="img/config/{{ $head_data['default_config']['favicon_image'] }}" style="width:32px" onclick="modalLoad(this)" /></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px">
                                <td id="currency-selector-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="currency_selection">Currency:</p>
                                </td>
                                <td id="currency-selector" style="width:250px;padding-bottom:20px">
                                    <select id="currency_selection" name="currency" placeholder="£" class="form-control theme-dropdown-alt" style="width:150px">
                                        <option alt="Pounds Sterling" value="£" @if ($head_data['config']['currency'] == '£') selected @endif>£ (Pound)</option>
                                        <option alt="Dollar"          value="$" @if ($head_data['config']['currency'] == '$') selected @endif>$ (Dollar)</option>
                                        <option alt="Euro"            value="€" @if ($head_data['config']['currency'] == '€') selected @endif>€ (Euro)</option>
                                        <option alt="Yen"             value="¥" @if ($head_data['config']['currency'] == '¥') selected @endif>¥ (Yen)</option>
                                        <option alt="Franc"           value="₣" @if ($head_data['config']['currency'] == '₣') selected @endif>₣ (Franc)</option>
                                        <option alt="Rupee"           value="₹" @if ($head_data['config']['currency'] == '₹') selected @endif>₹ (Rupee)</option>
                                        <option alt="Mark"            value="₻" @if ($head_data['config']['currency'] == '₻') selected @endif>₻ (Mark)</option>
                                        <option alt="Ruouble"         value="₽" @if ($head_data['config']['currency'] == '₽') selected @endif>₽ (Ruouble)</option>
                                        <option alt="Lira"            value="₺" @if ($head_data['config']['currency'] == '₺') selected @endif>₺ (Lira)</option>
                                    </select>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['currency'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['currency'] }}</span></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px">
                                <td id="sku-prefix-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="sku_prefix"><or class="title" title="Prefix for SKU element on stock. e.g. ITEM-00001 or SKU-00001">SKU Prefix:</or></p>
                                </td>
                                <td id="sku-prefix-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control theme-input-alt" type="text" style="width: 150px" id="sku_prefix" name="sku_prefix">
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['sku_prefix'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['sku_prefix'] }}</span></label>
                                </td>
                            </tr>

                            <tr class="" style="margin-top:20px">
                                <td id="base-url-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class="align-middle" for="base_url"><or class="title" title="This only changes the URL for any links or emails, not the web connection url. This needs to be changed in the web config file.">Base URL:</or></p>
                                </td>
                                <td id="base-url-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control theme-input-alt" type="text" style="width: 150px" id="base_url" name="base_url">
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['base_url'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['base_url'] }}</span></label>
                                </td>
                            </tr>

                            <tr class="" style="margin-top:20px">
                                <td id="default-theme-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="default_theme_id">Default Theme:</p>
                                </td>
                                <td id="default-theme-set" style="width:250px;padding-bottom:20px">
                                    <select id="default_theme_selection" name="default_theme_id" placeholder="Dark" class="form-control theme-dropdown-alt" style="width:150px">
                                    @if ($themes['count'] > 0)
                                        @foreach ($themes['rows'] as $theme) 
                                        <option value="{{ $theme['id'] }}" @if ($head_data['config']['default_theme_id'] == $theme['id']) selected @endif title="{{ $theme['file_name'] }}">{{ $theme['name'] }}</option>
                                        @endforeach
                                    @else
                                        <option selected disabled>No themes found</option>
                                    @endif
                                    </select>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['config']['default_theme_id'] }}</span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni">{{ $head_data['default_config']['default_theme_id'] }}</span></label>
                                </td>
                            </tr>


                            <tr class="" style="margin-top:20px;margin-left:25px;padding-bottom:20px">
                                <td style="width:250px">
                                    <input id="global-submit" type="submit" name="global-submit" class="btn btn-success" value="Save" />
                                </td>
                                <td style="width:250px;padding-bottom:20px">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px">
                                    <input id="global-restore-defaults" type="submit" name="global-restore-defaults" class="btn btn-danger" style="margin-left:15px" value="Restore Default" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>