import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Demandes } from './demande';

describe('Demande', () => {
  let component: Demandes;
  let fixture: ComponentFixture<Demandes>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Demandes]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Demandes);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
