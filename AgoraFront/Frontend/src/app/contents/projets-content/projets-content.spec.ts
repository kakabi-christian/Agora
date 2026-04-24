import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProjetsContentComponent } from './projets-content';

describe('ProjetsContentComponent', () => {
    let component: ProjetsContentComponent;
    let fixture: ComponentFixture<ProjetsContentComponent>;

    beforeEach(async () => {
        await TestBed.configureTestingModule({
            imports: [ProjetsContentComponent]
        })
            .compileComponents();

        fixture = TestBed.createComponent(ProjetsContentComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it('should create', () => {
        expect(component).toBeTruthy();
    });
});
