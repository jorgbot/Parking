function tt(param1, param2) {
    var id         = null,
        locale     = null,
        parameters = {},
        number     = 0,
        plurals    = false,
        old_locale = Lang.getLocale(),
        translation;
    if (typeof param1 === 'object') {
        if (param1 === null)
            return Lang;
        if (typeof param1['id'] !== 'undefined') {
            id         = param1['id'];
            locale     = typeof param1['locale'] !== 'undefined' ? param1['locale'] : locale;
            parameters = typeof param1['parameters'] !== 'undefined' ? param1['parameters'] : parameters;
            if (typeof param1['number'] !== 'undefined') {
                switch (typeof param1['number']) {
                    case 'boolean':
                        plurals = true;
                        number  = !param1['number'] ? 1 : 2;
                        break;
                    case 'number':
                        plurals = true;
                        number  = param1['number'];
                        break;
                }
            }
        } else {
            var data = [];
            $.each(param1, function(key, value) {
                data[key] = tt(value);
            });
            return data.join(' ');
        }
    } else if (typeof param1 === 'string') {
        id = param1;
        switch (typeof param2) {
            case 'string':
                locale = param2;
                break;
            case 'object':
                parameters = param2;
                break;
            case 'number':
                plurals = true;
                number  = param2;
                break;
            case 'boolean':
                plurals = true;
                number  = !param2 ? 1 : 2;
                break;
            case 'undefined':
                break;
            default:
                throw new Error("param2: Type not supported");
                break;
        }
    } else if (typeof param1 === 'undefined') {
        return Lang;
    } else
        throw new Error("param1: Type not supported");

    if (typeof lang_custom !== 'undefined') {
        var segments = id.split('.');
        var source   = segments[0];
        var entries  = segments.slice(1);
        var message  = lang_custom[source];
        if (typeof message !== 'undefined') {
            while (entries.length && (message = message[entries.shift()]));
            if (typeof message !== 'undefined')
                id = message;
            else if (typeof Lang.get(id) === 'object') {
                if (typeof lang_custom.country_iso !== 'undefined')
                    id += Lang.has(id + '.' + lang_custom.country_iso) ? '.' + lang_custom.country_iso : '.xx';
                else
                    id += '.xx';
            }
        }
    }

    if (locale !== null)
        Lang.setLocale(locale);

    if (plurals)
        translation = Lang.choice(id, number, parameters);
    else
        translation = typeof Lang.get(id, parameters) === 'string'
            ? Lang.get(id, parameters).split('|')[0]
            : Lang.get(id, parameters);

    if (locale !== null)
        Lang.setLocale(old_locale);

    return translation;
}

