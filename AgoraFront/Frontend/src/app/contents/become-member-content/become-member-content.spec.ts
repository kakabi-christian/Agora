import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BecomeMemberContent } from './become-member-content';

describe('BecomeMemberContent', () => {
  let component: BecomeMemberContent;
  let fixture: ComponentFixture<BecomeMemberContent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BecomeMemberContent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(BecomeMemberContent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
