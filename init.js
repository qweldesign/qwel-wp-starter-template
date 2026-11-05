// Action Core
import ActionCore from './js/action-core.js';

// Fader
import Fader from './js/fader.js';

// Modal
import Modal from './js/modal.js';

// Slider
import Slider from './js/slider.js';

new ActionCore.ActiveHeader({
  headerSelector: '.header'
});

new ActionCore.ScrollSpy({
  spySectionSelector: '.section__inner',
  spyNavSelector: '.wp-block-navigation-item'
});

new ActionCore.ReadableOnScroll({
  readableSelector: 'main > .wp-block-template-part:not(.hero)'
});

new ActionCore.BackToTop();

new ActionCore.DrawerMenu({
  siteBrandSelector: '.gNav__siteBrand',
  primaryMenuSelector: '.gNav__primaryMenu',
  socialMenuSelector: '.gNav__socialMenu'
});

new ActionCore.SafeEmbed({
  embedSelector: '.embed'
});

new Fader();
new Modal();
new Slider();
