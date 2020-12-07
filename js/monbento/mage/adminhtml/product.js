Product.Gallery.prototype.updateVisualisation = Product.Gallery.prototype.updateVisualisation.wrap(function (parentMethod, file) {
    var image = this.getImageByFile(file);
    this.getFileElement(file, 'cell-label input').value = image.label;
    this.getFileElement(file, 'cell-position input').value = image.position;
    this.getFileElement(file, 'cell-remove input').checked = (image.removed == 1);
    this.getFileElement(file, 'cell-disable input').checked = (image.disabled == 1);
    this.getFileElement(file, 'cell-gallery1 input').checked = (image.gallery1 == 1);
    this.getFileElement(file, 'cell-gallery2 input').checked = (image.gallery2 == 1);
    this.getFileElement(file, 'cell-gallery3 input').checked = (image.gallery3 == 1);
    this.getFileElement(file, 'cell-gallery4 input').checked = (image.gallery4 == 1);
    this.getFileElement(file, 'cell-gallery5 input').checked = (image.gallery5 == 1);
    this.getFileElement(file, 'cell-gallery6 input').checked = (image.gallery6 == 1);
    $H(this.imageTypes)
            .each(
                    function (pair) {
                        if (this.imagesValues[pair.key] == file) {
                            this.getFileElement(file,
                                    'cell-' + pair.key + ' input').checked = true;
                        }
                    }.bind(this));
    this.updateState(file);
});

Product.Gallery.prototype.updateImage = Product.Gallery.prototype.updateImage.wrap(function (parentMethod, file) {
    var index = this.getIndexByFile(file);
    this.images[index].label = this
            .getFileElement(file, 'cell-label input').value;
    this.images[index].position = this.getFileElement(file,
            'cell-position input').value;
    this.images[index].removed = (this.getFileElement(file,
            'cell-remove input').checked ? 1 : 0);
    this.images[index].disabled = (this.getFileElement(file,
            'cell-disable input').checked ? 1 : 0);
    this.images[index].gallery1 = (this.getFileElement(file,
            'cell-gallery1 input').checked ? 1 : 0);
    this.images[index].gallery2 = (this.getFileElement(file,
            'cell-gallery2 input').checked ? 1 : 0);
    this.images[index].gallery3 = (this.getFileElement(file,
            'cell-gallery3 input').checked ? 1 : 0);
    this.images[index].gallery4 = (this.getFileElement(file,
            'cell-gallery4 input').checked ? 1 : 0);
    this.images[index].gallery5 = (this.getFileElement(file,
            'cell-gallery5 input').checked ? 1 : 0);
    this.images[index].gallery6 = (this.getFileElement(file,
            'cell-gallery6 input').checked ? 1 : 0);
    this.getElement('save').value = Object.toJSON(this.images);
    this.updateState(file);
    this.container.setHasChanges();
});