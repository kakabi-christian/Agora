import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BecomeMemberContent } from '../../contents/become-member-content/become-member-content';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-member',
  standalone:true,
  imports: [CommonModule,BecomeMemberContent],
  templateUrl: './member.html',
  styleUrl: './member.css',
})
export class Member {

}
