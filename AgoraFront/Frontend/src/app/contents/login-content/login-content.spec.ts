import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LoginContent } from './login-content';

describe('LoginContent', () => {
  let component: LoginContent;
  let fixture: ComponentFixture<LoginContent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [LoginContent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(LoginContent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
