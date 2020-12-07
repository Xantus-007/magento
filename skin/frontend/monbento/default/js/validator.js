Validation.add('validate-consent', validateConsentMsg, function(v) {
    return !Validation.get('IsEmpty').test(v);
});
Validation.add('validate-fiscalid', 'Inserire un codice fiscale valido. Per esempio aaaaaa11a11a111a.', function (v) {
    var regexFiscalId = /^[a-zA-Z-]{6}[0-9]{2}[a-zA-Z-][0-9]{2}[a-zA-Z-][0-9]{3}[a-zA-Z-]$/;
    return regexFiscalId.test(v);
});
Validation.add('zipcode-fr', 'Ce code postal ne correspond pas à la France Métropolitaine', function (v, elm) {
    var regexZipCodeFr = /^(([0-8][0-9])|(9[0-5]))[0-9]{3}$/,
        msg = Translator.translate('This postal code does not correspond to Metropolitan France, for the French overseas departments and territories select the corresponding country.'),
        selects = elm.up('form').select('.country-select-form'),
        select;

    if (selects.length > 0) {
        for (var i = 0; i < selects.length; i++) {
            if (selects[i] !== false) {
                select = selects[i].select('select')[0];
                break;
            }
        }
    }        
    if (select) {
        if (select.getValue() === 'FR' && !regexZipCodeFr.test(v)) {
            alert(msg);
            return false;
        }
    }        
    return true;
});