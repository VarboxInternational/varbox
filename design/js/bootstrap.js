window.Quill = require('quill');

import { ImageUpload } from 'quill-image-upload';

let Size = Quill.import('attributors/style/size');
Size.whitelist = [false, '14px', '16px', '18px', '22px', '24px', '32px'];

Quill.register('modules/imageUpload', ImageUpload);
Quill.register(Size, true);
