import { TestBed } from '@angular/core/testing';

import { PartenaireService } from './partenaires';

describe('Partenaires', () => {
  let service: PartenaireService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(PartenaireService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
