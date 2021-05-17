var config = {
    paths: {
        'webslidemenu':'Sportpat_Menu::js/webslidemenu',
    } ,
    shim: {
        jquery: {
            exports: '$'
        },
        'bootstrap': {
            'deps': ['jquery']
        },
        'webslidemenu': {
            'deps': ['jquery','bootstrap']
        },
        'headmenu': {
            'deps': ['jquery']
        },
        'configswatch': {
            'deps': ['jquery']
        },
        'custom': {
            'deps':['jquery','jquery/ui']
        }
    },
    map: {
        '*': {

            'webslidemenu':'Sportpat_Menu::js/webslidemenu',
            'quickSearch-original': 'Magento_Search/js/form-mini',
            headmenu: 'js/headmenu',
            configswatch: 'Magento_ConfigurableProduct/js/configswatch',
            custom: 'Magento_Catalog/js/custom'
        }
    }
};