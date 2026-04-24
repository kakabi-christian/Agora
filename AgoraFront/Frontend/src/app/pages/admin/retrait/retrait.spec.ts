import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Retrait } from './retrait';

describe('Retrait', () => {
  let component: Retrait;
  let fixture: ComponentFixture<Retrait>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Retrait]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Retrait);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
