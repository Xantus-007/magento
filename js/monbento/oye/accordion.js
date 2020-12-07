OyeAccordion.prototype.openSection = OyeAccordion.prototype.openSection.wrap(function (parentMethod, section) {
    var section = $(section);
    
    jQuery('html, body').animate({
        scrollTop: jQuery("h1").offset().top + 40
    }, 700);
    
    jQuery('#checkoutStepsContent > li').each(function(index, element) {
        if(jQuery(element).hasClass('allow')) jQuery('#step-' + jQuery(element).attr('id')).addClass('allow');
    });

    // Check allow
    if (this.checkAllow && !Element.hasClassName(section, 'allow')) {
        return;
    }

    if (section.id != this.currentSection) {
        this.closeExistingSection();
        this.currentSection = section.id;
        $(this.currentSection).addClassName('active');
        $$("." + this.currentSection).first().addClassName('active');

        var contents = Element.select(section, '.a-item');
        contents[0].show();
        
        if(typeof(dataPush) != "undefined")
        {
            var number = jQuery('#checkoutSteps .' + this.currentSection + ' .number').text();
            var title = jQuery('#checkoutSteps .' + this.currentSection + ' .c-label').text();
            dataPush.ecommerce.checkout.actionField.step = parseInt(number) + deltaStepForGTM;
            dataPush.ecommerce.checkout.actionField.option = title;
            console.log(dataPush);
            dataLayer.push(dataPush);
        }
        //Effect.SlideDown(contents[0], {duration:.2});

        if (this.disallowAccessToNextSections) {
            var pastCurrentSection = false;
            for (var i = 0; i < this.sections.length; i++) {
                if (pastCurrentSection) {
                    Element.removeClassName(this.sections[i], 'allow');
                    $$('.' + this.sections[i]).first().removeClassName('allow');
                }
                if (this.sections[i].id == section.id) {
                    pastCurrentSection = true;
                }
            }
        }
    }

    if (section.id == 'opc-review')
    {
        $$(".c-checkout__content").first().addClassName('c-review');
    } else
    {
        $$(".c-checkout__content").first().removeClassName('c-review');
    }

});
