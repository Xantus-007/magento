VarienForm.prototype.submit = VarienForm.prototype.submit.wrap(function(parentMethod){
    if(this.validator && this.validator.validate()){
        this.form.submit();
    }
    $$('body')[0].fire('dbm:ItemEqualizer');
    return false;
});
