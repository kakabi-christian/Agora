import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EvenementsContentComponent } from './evenements-content';

describe('EvenementsContentComponent', () => {
    let component: EvenementsContentComponent;
    let fixture: ComponentFixture<EvenementsContentComponent>;

    beforeEach(async () => {
        await TestBed.configureTestingModule({
            imports: [EvenementsContentComponent]
        })
            .compileComponents();

        fixture = TestBed.createComponent(EvenementsContentComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it('should create', () => {
        expect(component).toBeTruthy();
    });
});
