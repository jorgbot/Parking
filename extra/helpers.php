<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 1/02/18
 * Time: 11:34 AM
 */
use Illuminate\Support\Facades\Auth;
use App\Currency;
use App\CurrenciesConversion;
use App\CurrenciesConversionLog;
if (! function_exists('tt')) {
    /**
     *
     * I18n generica ver documentación
     * @param string/array $param1
     * @param string/array/number/boolean $param2 [Optional]
     * @return string|array
     *
     * @author Juan Esteban Moreno
     */
    function tt($param1 = null, $param2 = null) {
        $id = null;
        $locale = null;
        $parameters = [];
        $number = 0;
        $plurals = false;
        $id_empresa = null;
        if(gettype($param1)=='string') {
            $id = $param1;
            switch(gettype($param2)) {
                case 'string':
                    switch ($param2) {
                        case TT_CONFIG:
                            $array = config($param1);
                            foreach ($array as $key => $value)
                                $array[$key] = tt($value);
                            return $array;
                            break;
                        case TT_EQUIVALENCES:
                            if (is_array($lang = trans($id))) {
                                $lang = array_map('strtolower', $lang);
                                return array_map('revert_slug', array_flip($lang));
                            }
                    }
                    $locale = $param2;
                    break;
                case 'array':
                    $parameters = $param2;
                    break;
                case 'integer':
                    $plurals = true;
                    $number = $param2;
                    break;
                case 'boolean':
                    $plurals = true;
                    $number = $param2 == TT_SINGULAR ? 1 : 0;
                    break;
                case "NULL":
                    break;
                default:
                    throw new Exception('$param2: Type not supported');
                    break;
            }
        } else if ($param1 === null)
            return trans();
        else
            throw new Exception('$param1: Type not supported');

        if (session()->has(LANG_PREFIX.$id))
            $id = session(LANG_PREFIX.$id);
        else if(getType(trans($id)) == 'array')
            $id .= '.xx';

        if($plurals)
            return trans_choice($id, $number, $parameters, $locale);
        return explode('|',trans($id, $parameters, $locale))[0];
    }
}

if (! function_exists('accents')) {
    function accents($subject){
        $search  = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±,Ã“,Ã ,Ã‰,Ã ,Ãš,â€œ,â€ ,Â¿,ü,Ã‘,â€¨,Â");
        $replace = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ó,Á,É,Í,Ú,\",\",¿,&uuml;,Ñ,,&nbsp;");
        $s = str_replace($search, $replace, $subject);
        $s = str_replace("\u00c3\u0080", "&Agrave;", $s);
        $s = str_replace(["\u00c3\u0081", 'Á'], "&Aacute;", $s);
        $s = str_replace("\u00c3\u0082", "&Acirc;", $s);
        $s = str_replace("\u00c3\u0083", "&Atilde;", $s);
        $s = str_replace("\u00c3\u0084", "&Auml;", $s);
        $s = str_replace("\u00c3\u0085", "&Aring;", $s);
        $s = str_replace("\u00c3\u0086", "&AElig;", $s);
        $s = str_replace("\u00c3\u00a0", "&agrave;", $s);
        $s = str_replace(["\u00c3\u00a1", 'á'], "&aacute;", $s);
        $s = str_replace("\u00c3\u00a2", "&acirc;", $s);
        $s = str_replace("\u00c3\u00a3", "&atilde;", $s);
        $s = str_replace("\u00c3\u00a4", "&auml;", $s);
        $s = str_replace("\u00c3\u00a5", "&aring;", $s);
        $s = str_replace("\u00c3\u00a6", "&aelig;", $s);
        $s = str_replace("\u00c3\u0087", "&Ccedil;", $s);
        $s = str_replace("\u00c3\u00a7", "&ccedil;", $s);
        $s = str_replace("\u00c3\u0090", "&ETH;", $s);
        $s = str_replace("\u00c3\u00b0", "&eth;", $s);
        $s = str_replace("\u00c3\u0088", "&Egrave;", $s);
        $s = str_replace(["\u00c3\u0089", 'É'], "&Eacute;", $s);
        $s = str_replace("\u00c3\u008a", "&Ecirc;", $s);
        $s = str_replace("\u00c3\u008b", "&Euml;", $s);
        $s = str_replace("\u00c3\u00a8", "&egrave;", $s);
        $s = str_replace(["\u00c3\u00a9", 'é'], "&eacute;", $s);
        $s = str_replace("\u00c3\u00aa", "&ecirc;", $s);
        $s = str_replace("\u00c3\u00ab", "&euml;", $s);
        $s = str_replace("\u00c3\u008c", "&Igrave;", $s);
        $s = str_replace(["\u00c3\u008d", 'Í'], "&Iacute;", $s);
        $s = str_replace("\u00c3\u008e", "&Icirc;", $s);
        $s = str_replace("\u00c3\u008f", "&Iuml;", $s);
        $s = str_replace("\u00c3\u00ac", "&igrave;", $s);
        $s = str_replace(["\u00c3\u00ad", 'í'], "&iacute;", $s);
        $s = str_replace("\u00c3\u00ae", "&icirc;", $s);
        $s = str_replace("\u00c3\u00af", "&iuml;", $s);
        $s = str_replace("\u00c3\u0091", "&Ntilde;", $s);
        $s = str_replace("\u00c3\u00b1", "&ntilde;", $s);
        $s = str_replace("\u00c3\u0092", "&Ograve;", $s);
        $s = str_replace(["\u00c3\u0093", 'Ó'], "&Oacute;", $s);
        $s = str_replace("\u00c3\u0094", "&Ocirc;", $s);
        $s = str_replace("\u00c3\u0095", "&Otilde;", $s);
        $s = str_replace("\u00c3\u0096", "&Ouml;", $s);
        $s = str_replace("\u00c3\u0098", "&Oslash;", $s);
        $s = str_replace("\u00c5\u0092", "&OElig;", $s);
        $s = str_replace("\u00c3\u00b2", "&ograve;", $s);
        $s = str_replace(["\u00c3\u00b3", 'ó'], "&oacute;", $s);
        $s = str_replace("\u00c3\u00b4", "&ocirc;", $s);
        $s = str_replace("\u00c3\u00b5", "&otilde;", $s);
        $s = str_replace("\u00c3\u00b6", "&ouml;", $s);
        $s = str_replace("\u00c3\u00b8", "&oslash;", $s);
        $s = str_replace("\u00c5\u0093", "&oelig;", $s);
        $s = str_replace("\u00c3\u0099", "&Ugrave;", $s);
        $s = str_replace(["\u00c3\u009a", 'Ú'], "&Uacute;", $s);
        $s = str_replace("\u00c3\u009b", "&Ucirc;", $s);
        $s = str_replace("\u00c3\u009c", "&Uuml;", $s);
        $s = str_replace("\u00c3\u00b9", "&ugrave;", $s);
        $s = str_replace(["\u00c3\u00ba", 'ú'], "&uacute;", $s);
        $s = str_replace("\u00c3\u00bb", "&ucirc;", $s);
        $s = str_replace("\u00c3\u00bc", "&uuml;", $s);
        $s = str_replace("\u00c3\u009d", "&Yacute;", $s);
        $s = str_replace("\u00c5\u00b8", "&Yuml;", $s);
        $s = str_replace("\u00c3\u00bd", "&yacute;", $s);
        $s = str_replace("\u00c3\u00bf", "&yuml;", $s);
        return $s;
    }
}
if(!function_exists('script')){
    function script($file, $attributes = []) {
        return Html::script(last_version($file), $attributes, true);
    }
}

if(!function_exists('style')){
    function style($file, $attributes = []) {
        return Html::style(last_version($file), $attributes, true);
    }
}
if(!function_exists('last_version')){
    function last_version($file) {
        if (file_exists($file)) {
            return "$file?".filectime($file);
        }
        return $file;
    }
}
if (! function_exists('get_separators')) {
    function get_separators()
    {
        if(!empty(session('id_pais'))) {
            switch (session('id_pais')) {
                case COUNTRY_COLOMBIA:
                case COUNTRY_ARGENTINA:
                case COUNTRY_BRAZIL:
                case COUNTRY_COSTA_RICA:
                case COUNTRY_CHILE:
                case COUNTRY_VENEZUELA:
                case COUNTRY_URUGUAY:
                    $dec_point = ',';
                    $thousands_sep = '.';
                    break;
                default:
                    $dec_point = '.';
                    $thousands_sep = ',';
                    break;
            }
        } else {
            $dec_point = ',';
            $thousands_sep = '.';
        }
        return [$dec_point, $thousands_sep];
    }
}
if (! function_exists('format_number')) {
    function format_number($number, $decimals = 0, $dec_point = null, $thousands_sep = null) {
        list($dec_point, $thousands_sep) = get_separators();
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }
}
if (! function_exists('format_money')) {
    function format_money($number, $symbol = '$') {
        $decimals = count(explode('.', $number)) > 1 ? 2 : 0;
        return $symbol . format_number($number, $decimals);
    }
}
if (! function_exists('usd_to_local')) {
    function usd_to_local($params = array()) {
        $mode       = isset($params['mode']) ? $params['mode'] : 'value';
        $usd        = isset($params['usd']) ? $params['usd'] : 1;
        $country_id = isset($params['country_id']) ? $params['country_id'] : Auth::user()->id_pais;
        $currencies = config('payment.country');

        if (array_key_exists($country_id, $currencies)) {
            $currency = Currency::find($currencies[$country_id]['currency']);
            if (isset($params['id_currencies_log']))
                $unit = ($conversion = CurrenciesConversionLog::find($params['id_currencies_log'])) ? $conversion->value : config('payment.country.'.$country_id.'.usd_local');
            else
                $unit = ($conversion = CurrenciesConversion::where('currencies', 'usd_'.strtolower($currency->abreviacion))->first()) ? $conversion->value : config('payment.country.'.$country_id.'.usd_local');
            $total    = $unit * $usd;
            $pricing  = trans('payment.'.$country_id, [
                'abbr'  => $currency->abreviacion,
                'value' => format_money($total, $currency->simbolo)
            ]);
        } else {
            $currency = Currency::find(CURRENCY_USD);
            $unit     = config('payment.country.default.usd_local');
            $total    = $unit * $usd;
            $pricing  = trans('payment.', [
                'abbr'  => $currency->abreviacion,
                'value' => format_money($total, $currency->simbolo)
            ]);
        }

        switch ($mode) {
            case 'pricing':
                return $pricing;
            case 'short_label':
                return format_money($total, $currency->simbolo)." ".$currency->abreviacion;
            case 'label':
                return format_money($total, $currency->simbolo)." ".$currency->nombre_moneda;
            case 'currency':
                return $currency;
        }
        return $total;
    }
}
if (! function_exists('format_date')) {
    function format_date($date, $with_hour = false, $format = 'd/m/Y') {
        if ($date instanceof \Carbon\Carbon) {
            if ($with_hour)
                return $date->format($format.' h:i:s');
            return $date->format($format);
        }
        $array = explode(' ', $date);
        $date  = $array[0];
        $hour  = count($array) > 1 ? $array[1] : '';
        $date  = \Carbon\Carbon::parse($date)->format($format);
        return $with_hour && !empty($hour) ? $date . ' ' . $hour : $date;
    }
}
if(! function_exists('isBici') ) {
    function isBici()
    {
        return in_array(\Auth::user()->parking_id, config('templates.bicicletas'));
    }
}

if(! function_exists('isReport') ) {
    function isReport()
    {
        return in_array(\Auth::user()->parking_id, config('templates.report'));
    }
}

if(! function_exists('isconvenio') ) {
    function isconvenio()
    {
        return in_array(\Auth::user()->parking_id, config('templates.convenio'));
    }
}
if(! function_exists('isIva') ) {
    function isIva()
    {
        return in_array(\Auth::user()->parking_id, config('templates.iva'));
    }
}
if(! function_exists('onlyIva') ) {
    function onlyIva()
    {
        return in_array(\Auth::user()->parking_id, config('templates.onlyIva'));
    }
}
if(! function_exists('isJornada') ) {
    function isJornada()
    {
        return in_array(\Auth::user()->parking_id, config('templates.jornada'));
    }
}