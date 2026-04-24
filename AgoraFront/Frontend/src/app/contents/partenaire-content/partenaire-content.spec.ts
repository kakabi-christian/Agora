import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PartenaireContent } from './partenaire-content';

describe('PartenaireContent', () => {
  let component: PartenaireContent;
  let fixture: ComponentFixture<PartenaireContent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PartenaireContent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PartenaireContent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
