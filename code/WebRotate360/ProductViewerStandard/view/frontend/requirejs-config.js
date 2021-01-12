var config = {
    config: {
        mixins: {
            'mage/gallery/gallery': {
                'WebRotate360_ProductViewerStandard/wr360hook': true
            }
        },
    },

    map: {
        '*': {
            'imagerotator': 'WebRotate360_ProductViewerStandard/imagerotator/html/js/imagerotator',
            'product360view': 'WebRotate360_ProductViewerStandard/js/product360view'
        }
    }
};