import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Partenaires } from './partenaires';

describe('Partenaires', () => {
  let component: Partenaires;
  let fixture: ComponentFixture<Partenaires>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Partenaires]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Partenaires);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
