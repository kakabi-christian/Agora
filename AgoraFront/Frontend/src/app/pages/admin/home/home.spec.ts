import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HomeAdmin } from './home';

describe('Home', () => {
  let component: HomeAdmin;
  let fixture: ComponentFixture<HomeAdmin>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HomeAdmin]
    })
    .compileComponents();

    fixture = TestBed.createComponent(HomeAdmin);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
