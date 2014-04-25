#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <cmath>
#include <iostream>
#include <algorithm>
#include <set>
#include <map>
#include <vector>
#include <queue>
#include <stack>
#include <list>
#include <string>

#define SQR(_x) ((_x)*(_x))
//#define REP(_i,_n) for(int _i = 0; _i < (int)(_n); _i++)
//#define FOR(_i,_a,_b) for(int _i = (int)(_a); _i <= (int)(_b); _i++)
//#define BCK(_i,_a,_b) for(int _i = (int)(_a); _i >= (int)(_b); _i--)
#define NL printf("\n")
#define LL long long
#define INF 1000000000

using namespace std;

int ball[2000]={};

int main()
{
	int n,x;
	cin >> n;
	cin >> ball[n-1];
	ball[n]=INF+1;
	for(int i = 1; i < n; i++)
	{
		cin >> x;
		if(ball[n-i]>0)
		{
			for(int j = n-i; j <= n; j++)
			{
				if(ball[j]>x)
				{
					ball[j-1]=x;
					break;
				}
				else if(ball[j]==x)
				{
					ball[j-1]=x-1;
					break;
				}
				ball[j-1]=ball[j]-1;
				x--;
			}
		}
		else
		{
			ball[n-i-1]=x;
			for(int j = n-i; j <= n; j++)
			{
				if(ball[j]<=ball[j-1])
					ball[j]=ball[j-1]+1;
			}
		}
	}
	for(int i = n-1; i >= 0; i--)
	{
		cout << ball[i] << endl;
	}
	return 0;
}