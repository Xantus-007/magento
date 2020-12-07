Validation.add('validate-fiscalid', 'Inserire un codice fiscale valido. Per esempio aaaaaa11a11a111a.', function (v) {
    var regexFiscalId = /^[a-zA-Z-]{6}[0-9]{2}[a-zA-Z-][0-9]{2}[a-zA-Z-][0-9]{3}[a-zA-Z-]$/;
    return regexFiscalId.test(v);
});