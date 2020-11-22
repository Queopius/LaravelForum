import { mount } from '@vue/test-utils';
import expect from 'expect';
import Wysiwyg from '../../../resources/js/components/Wysiwyg.vue';

describe('Wysiwyg', () => {
	let component

	beforeEach(() => {
		component = mount(Wysiwyg)
	});
	it('name', () => {
		expect(component.html()).toContain('Wysiwyg')
	})
});