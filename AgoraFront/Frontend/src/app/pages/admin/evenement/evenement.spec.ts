import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Evenement } from './evenement';

describe('Evenement', () => {
  let component: Evenement;
  let fixture: ComponentFixture<Evenement>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Evenement]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Evenement);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
