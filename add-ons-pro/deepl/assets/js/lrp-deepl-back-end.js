jQuery(document).on('lrpInitFieldToggler', function() {
    var deeplKey = LRP_Field_Toggler()
        deeplKey.init('.lrp-translation-engine', '#lrp-deepl-key', 'deepl' )

    var deeplType = LRP_Field_Toggler()
        deeplType.init('.lrp-translation-engine', '#lrp-deepl-api-type-free', 'deepl' )
})
