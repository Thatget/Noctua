var config = {
  shim: {
    "Magento_Theme/js/jquery.dlmenu": {
      deps: ["jquery"],
    },
    "Magento_Theme/js/jquery.fancybox": {
      deps: ["jquery"],
    },
  },
  path: {
    // disable jQuery migrate console output
    "jquery/jquery-migrate": "js/jquery-migrate",
  },
  map: {
    "*": {
      sitlazyload: "Magento_Theme/js/sit_lazyload",
      dlmenu: "Magento_Theme/js/jquery.dlmenu",
      modernizr: "Magento_Theme/js/modernizr.custom",
      changeview: "Magento_Theme/js/changeview",
      fancybox: "Magento_Theme/js/jquery.fancybox",
    },
  },
  deps: ["Magento_Theme/js/sit"],
};
