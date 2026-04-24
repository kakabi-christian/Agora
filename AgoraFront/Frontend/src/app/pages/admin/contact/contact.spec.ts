import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdminContactComponent } from './contact' ;

describe('Contact', () => {
  let component: AdminContactComponent;
  let fixture: ComponentFixture<AdminContactComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminContactComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AdminContactComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
